<?php


namespace App\Controller;


use App\Entity\Team;
use App\Entity\Tournament;
use App\Repository\TournamentRepository;
use Psr\Container\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TournamentController extends AbstractController
{

    /**
     * @var TournamentRepository
     */
    private $tournamentRepository;

    /**
     * TournamentController constructor.
     * @param TournamentRepository $tournament
     */
    public function __construct(TournamentRepository $tournament)
    {
        $this->tournamentRepository = $tournament;
    }

    /**
     * @param $team
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Exception
     *
     * funkce pro tvormu formuláře k editaci či přidání turnaje
     */
    public function make_form($team)
    {
        // vytvoření formuláře pro přidání záznamu
        $form = $this->createFormBuilder($team)
            ->add('name', TextType::class, array(
                'label' => 'Název',
                'attr' => array('class' => 'form-control')
            ))
            ->add('date', DateType::class, array(
                'label' => 'Datum',
                'widget' => 'choice',
                'required' => true,
                'input'  => 'datetime',
                'format' => 'dd. MM. yyyy',
                'data' => new \DateTime(),
                'attr' => array('class' => 'form-control')
            ))
            ->add('price', IntegerType::class, array(
                'label' => 'Cena',
                'attr' => array('class' => 'form-control')
            ))
            ->add('plays_in_game', IntegerType::class, array(
                'label' => 'Počet her na set',
                'attr' => array('class' => 'form-control')
            ))
            ->add('max_teams_count', IntegerType::class, array(
                'label' => 'Max. počet týmů',
                'required' => false,
                'attr' => array('class' => 'form-control')
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Uložit',
                'attr' => array('class' => 'btn btn btn-success mt-3 showloading', 'data-dissmiss' => 'modal')
            ))
            ->getForm();
        return $form;
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @Route("/tournaments/delete/{id}", methods={"DELETE"})
     *
     * funkce k odstranění turnaje z databáze
     */
    public function delete(Request $request, $id) {
        $tournament = $this->getDoctrine()->getRepository(Tournament::class)->find($id);
        if (!($this->getUser()->getEmail() == $tournament->getAdminString() or $this->getUser()->hasRole("ROLE_ADMIN"))) {
            //dump($tournament);
            exit;
        }
        $teams = $tournament->getTeams();
        $request = new Request();
        foreach ($teams as $team) {
            $this->remove_team($request, $id, $team->getId());
        }

        $games = $tournament->getGames();
        foreach ($games as $game) {
            $tournament->removeGame($game);
        }

        //dump($this->getUser());
        //dump($tournament->getAdminString());

        $entityManager = $this->getDoctrine()->getManager();
        try {
            $entityManager->remove($tournament);
            $entityManager->flush();
        } catch (\Exception $e) {
            $this->addFlash('error', 'Turnaj \'' . $tournament->getName() . '\' již je rozehrán není možné jej odstranit.');
            return new Response();
        }

        // vytvoření flash oznámení
        $this->addFlash('warning', 'Turnaj \'' . $tournament->getName() . '\' byl odstraněn.');
        return new Response();
    }

    /**
     * @param Request $request
     * @param $tournament_id
     * @param $team_id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/tournament/{tournament_id}/unlink/{team_id}", methods={"DELETE"})
     *
     * funkce k odstranění týmu
     */
    public function remove_team(Request $request, $tournament_id, $team_id) {
        $tournament = $this->getDoctrine()->getRepository(Tournament::class)->find($tournament_id);
        $team = $this->getDoctrine()->getRepository(Team::class)->find($team_id);
        if (!($team)) {
            $this->addFlash('error', 'Tým s id \'' . $team . '\' neexistuje.');
            return $this->redirect($this->generateUrl('/bring_me_back'));
        }
        if (!($tournament)) {
            $this->addFlash('error', 'Turnaj s id \'' . $tournament_id . '\' neexistuje.');
            return $this->redirect($this->generateUrl('/bring_me_back'));
        }
        if ($this->getUser()->getEmail() != $tournament->getAdminString() and !($this->getUser()->hasRole("ROLE_ADMIN"))
            and $this->getUser()->getEmail() != $team->getAdminString()) {
            $this->addFlash('error', 'Tým \'' . $team->getName() . '\' nemůžete odebrat z turnaje \'' . $tournament->getName() . '\'.');
            return $this->redirect($this->generateUrl('/bring_me_back'));
        }
        $tournament->removeTeam($team);
        $this->getDoctrine()->getManager()->persist($tournament);
        $this->getDoctrine()->getManager()->persist($team);
        $this->addFlash('warning', 'Tým \'' . $team->getName() . '\' byl úspěšně odebrán z turnaje \'' . $tournament->getName() . '\'.');
        $this->getDoctrine()->getManager()->flush();
        return new Response();
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     * @Route("/tournaments/edit/{id}", methods={"GET", "POST"})
     *
     * funkce k editaci turnaje
     */
    public function edit(Request $request, $id) {
        $title = "Editace turnaje";
        $tournament = $this->getDoctrine()->getRepository(Tournament::class)->find($id);
        if (!($tournament)) {
            $this->addFlash('error', 'Turnaj s id \'' . $id . '\' neexistuje.');
            return $this->redirect($this->generateUrl('/bring_me_back'));
        }
        if (!($this->getUser() != null and ($this->getUser()->getEmail() == $tournament->getAdminString() or $this->getUser()->hasRole("ROLE_ADMIN")))) {
            $this->addFlash('error', 'Turnaj \'' . $tournament->getName() . '\' nemáte oprávnění upravovat.');
            return $this->redirect($this->generateUrl('/bring_me_back'));
        }
        $formedit = $this->make_form($tournament);

        // Zpracování add formuláře.
        $formedit->handleRequest($request);
        if ($formedit->isSubmitted()) {
            if ($formedit->isValid()) {
                $this->tournamentRepository->save($tournament);
                $this->addFlash('success', 'Turnaj \'' . $tournament->getName() . '\' byl úspěšně editován.');
                return $this->redirect($this->generateUrl('/bring_me_back'));
            } else {
                $this->addFlash('error', 'Turnaj nemohl být editován, špatně vyplněný formulář.');
            }
        }

        return $this->render('pages/tables/edit.html.twig', array('title' => $title, 'formedit' => $formedit->createView()));
    }


    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/tournaments/detail/{id}", methods={"GET", "POST"})
     *
     * funkce k zobrazeí detailu turnaje
     */
    public function show(Request $request, $id)
    {
        $tournament = $this->getDoctrine()->getRepository(Tournament::class)->find($id);
        if (!($tournament)) {
            $this->addFlash('error', 'Turnaj s id \'' . $id . '\' neexistuje.');
            return $this->redirect($this->generateUrl('/bring_me_back'));
        }
        $title = "Detail turnaje '" . $tournament->getName() . "'";
        $tournament_date = $tournament->getDate()->format('d. m. Y');

        $table['name'] = "teams";
        $table['headers'] = array("Název");
        $table['rows'] = array();
        $admin = $tournament->getAdminString();

        // naplnění struktury pro výpis tabulky
        // TODO změnit na správenj tým
        $teams = $tournament->getTeams();
        $team = null;
        foreach($teams as $team){
            $row['link'] = true;
            $row['id'] = $team->getId();
            $row['data'] = array($team->getName());
            $row["admin"] = $team->getAdminString();
            array_push($table['rows'], $row);
        }

        $all_teams = null;
        $form_teams = null;
        if ($this->getUser() != null) {
            if ($tournament->getAdminString() == $this->getUser()->getEmail() or $this->getUser()->hasRole("ROLE_ADMIN")) {
                $all_teams = $this->getDoctrine()->getRepository(Team::class)->findAll();
            } else {
                $all_teams = $this->getUser()->getTeams();
            }
            //$all_teams = $tournament->getAdmin()->getTeams();
            foreach ($all_teams as $team) {
                // slouží k výpisu jen hráču, co ještě nejsou v týmu
                if (!$teams->contains($team)) {
                    $form_teams[$team->getName()] = $team->getId();
                }
            }
        }

        $add_team = new Team();
        // Dělám tady trochu bordel, sry, do name si uložím ID toho hráča, protože nemám setID, tak to dělám přes setName
        // Dole to pak použiju k vyhledání toho hráča, co se má uložit
        $formadd = $this->createFormBuilder($add_team)
            ->add('name', ChoiceType::class, array(
                'choices'  => $form_teams,
                'attr' => array('class' => 'custom-select'),
                'placeholder' => " ",
                'label' => 'Dostupné týmy' ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Uložit',
                'attr' => array('class' => 'btn btn btn-success mt-3 showloading', 'data-dissmiss' => 'modal')) )
            ->getForm();

        // Zpracování add formuláře.
        $formadd->handleRequest($request);
        if ($formadd->isSubmitted()) {
            if ($formadd->isValid()) {
                // sic je tady getName, tak do name jsem výše uložil ID toho hráča, takže se hledá podle ID, sorry.
                $team = $this->getDoctrine()->getRepository(Team::class)->find($add_team->getName());
                $tournament->addTeam($team);
                $this->getDoctrine()->getManager()->persist($tournament);
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'Tým \'' . $team->getName() . '\' byl úspěšně přidán do turnaje \'' . $tournament->getName() . '\'.');
                return $this->redirect($request->getUri());
            } else {
                $this->addFlash('error', 'Tým nemohl být přidán, špatně vyplněný formulář.');
            }
        }

        return $this->render('pages/details/tournament.html.twig', array('title' => $title,
            'tournament' => $tournament, 'table' => $table, 'formadd' => $formadd->createView(),
            'tournament_date' => $tournament_date, 'admin' => $admin, "id" => $id));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     * @Route("/tournaments", name="/tournaments", methods={"GET", "POST"})
     *
     * funkce k zobrazení všech hráčů v turnaji
     */
    public function index(Request $request)
    {
        // promněnné pro výpis
        $table_name = "Tabulka turnajů";
        $table['name'] = "tournaments";
        $table['headers'] = array("Název", "Datum", "Cena", "Organizátor", "Počer her na set", "Maximální počet týmů");
        $table['rows'] = array();

        // naplnění struktury pro výpis tabulky
        $tournaments = $this->getDoctrine()->getRepository(Tournament::class)->findAll();
        $tournament = null;
        foreach ($tournaments as $tournament) {
            $row['data'] = array($tournament->getName(), $tournament->getDate()->format('d. m. Y'), $tournament->getPrice(), $tournament->getAdminString(), $tournament->getPlaysInGame(), $tournament->getMaxTeamsCount());
            $row['link'] = true;

            $row['id'] = $tournament->getId();
            $row["name"] = $tournament->getName();
            $row["date"] = $tournament->getDate()->format('d. m. Y');
            $row["price"] = $tournament->getPrice();
            $row["plays_in_game"] = $tournament->getPlaysInGame();
            $row["max_teams_count"] = $tournament->getMaxTeamsCount();
            $row["admin"] = $tournament->getAdminString();

            array_push($table['rows'], $row);
        }

        $new_tournament = new Tournament();
        $formadd = $this->make_form($new_tournament);

        // Zpracování add formuláře.
        $formadd->handleRequest($request);
        if ($formadd->isSubmitted()) {
            if ($formadd->isValid()) {
                $new_tournament->setAdmin($this->getUser());
                $this->tournamentRepository->save($new_tournament);
                $this->addFlash('success', 'Turnaj \'' . $new_tournament->getName() . '\' byl úspěšně přidán.');
                return $this->redirect($request->getUri());
            } else {
                $this->addFlash('error', 'Turnaj nemohl být přidán, špatně vyplněný formulář.');
            }
        }

        return $this->render('pages/tables/pages/tournaments.html.twig', array('table_name' => $table_name,
            'formadd' => $formadd->createView(), 'table' => $table));
    }
}