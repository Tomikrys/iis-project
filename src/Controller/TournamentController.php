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
                'input'  => 'datetime',
                'format' => 'dd. MM. yyyy',
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
                'attr' => array('class' => 'form-control')
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Uložit',
                'attr' => array('class' => 'btn btn btn-success mt-3', 'data-dissmiss' => 'modal')
            ))
            ->getForm();
        return $form;
    }

    /**
     * @param Request $request
     * @param $id
     * @Route("/tournament/delete/{id}", methods={"DELETE"})
     */
    public function delete(Request $request, $id) {
        $tournament = $this->getDoctrine()->getRepository(Tournament::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($tournament);
        $entityManager->flush();
        // vytvoření flash oznámení
        $this->addFlash('warning', 'Turnaj \'' . $tournament->getName() . '\' byl odstraněn.');
        $response = new Response();
        $response->send();
    }

    /**
     * @param Request $request
     * @param $id
     * @Route("/tournament/unlink/{id}", methods={"DELETE"})
     */
    public function remove_team(Request $request, $id) {
        $team = $this->getDoctrine()->getRepository(Team::class)->find($id);
        $this->removeTeam($team);
    }

    /**
     * @Route("/tournaments/edit/{id}")
     * @Method({"GET", "POST"})
     */
    public function edit(Request $request, $id) {
        $title = "Editace turnaje";
        $tournament = $this->getDoctrine()->getRepository(Tournament::class)->find($id);
        $formedit = $this->make_form($tournament);

        // Zpracování add formuláře.
        $formedit->handleRequest($request);
        if ($formedit->isSubmitted() && $formedit->isValid()) {
            $this->tournamentRepository->save($tournament);
            $this->addFlash('success', 'Turnaj \'' . $tournament->getName() . '\' byl úspěšně editován.');
            return $this->redirect($this->generateUrl('/tournaments'));
        }

        return $this->render('pages/tables/edit.html.twig', array('title' => $title, 'formedit' => $formedit->createView()));
    }


    /**
     * @Route("/tournaments/detail/{id}")
     * @Method({"GET", "POST"})
     */
    public function show(Request $request, $id)
    {
        $tournament = $this->getDoctrine()->getRepository(Tournament::class)->find($id);
        $title = "Detail turnaje '" . $tournament->getName() . "'";
        $tournament_date = $tournament->getDate()->format('d. m. Y');

        $table['name'] = "teams";
        $table['headers'] = array("Název");
        $table['rows'] = array();

        // naplnění struktury pro výpis tabulky
        // TODO změnit na správenj tým
        $teams = $tournament->getTeams();
        $team = null;
        foreach($teams as $team){
            $row['id'] = $team->getId();
            $row['data'] = array($team->getName());
            array_push($table['rows'], $row);
        }

        $all_teams = $this->getDoctrine()->getRepository(Team::class)->findAll();
        $form_teams = null;
        foreach ($all_teams as $team) {
            // slouží k výpisu jen hráču, co ještě nejsou v týmu
            if (!$teams->contains($team)) {
                $form_teams[$team->getName()] = $team->getId();
            }
        }

        $add_team = new Team();
        // Dělám tady trochu bordel, sry, do name si uložím ID toho hráča, protože nemám setID, tak to dělám přes setName
        // Dole to pak použiju k vyhledání toho hráča, co se má uložit
        $formadd = $this->createFormBuilder($add_team)
            ->add('name', ChoiceType::class, array(
                'choices'  => $form_teams,
                'attr' => array('class' => 'custom-select'),
                'label' => 'Dostupné týmy' ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Uložit',
                'attr' => array('class' => 'btn btn btn-success mt-3', 'data-dissmiss' => 'modal')) )
            ->getForm();

        // Zpracování add formuláře.
        $formadd->handleRequest($request);
        if ($formadd->isSubmitted() && $formadd->isValid()) {
            // sic je tady getName, tak do name jsem výše uložil ID toho hráča, takže se hledá podle ID, sorry.
            $team = $this->getDoctrine()->getRepository(Team::class)->find($add_team->getName());
            $tournament->addTeam($team);
            $this->getDoctrine()->getManager()->persist($tournament);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Tým \'' . $team->getName() . '\' byl úspěšně  do turnaje \'' . $tournament->getName() . '\'.');
            return $this->redirect($request->getUri());
        }

        return $this->render('pages/details/tournament.html.twig', array('title' => $title, 'tournament' => $tournament,
            'table' => $table, 'formadd' => $formadd->createView(), 'tournament_date' => $tournament_date));
    }

    /**
     * @Route("/tournaments", name="/tournaments", methods={"GET", "POST"})
     */
    public function index(Request $request)
    {
        // promněnné pro výpis
        $table_name = "Tabulka turnajů";
        $table['name'] = "tournaments";
        $table['headers'] = array("Název", "Datum", "Cena", "Počer her na set", "Maximální počet týmů");
        $table['rows'] = array();

        // naplnění struktury pro výpis tabulky
        $tournaments = $this->getDoctrine()->getRepository(Tournament::class)->findAll();
        $tournament = null;
        foreach ($tournaments as $tournament) {
            $row['id'] = $tournament->getId();
            $row['data'] = array($tournament->getName(), $tournament->getDate()->format('d. m. Y'), $tournament->getPrice(), $tournament->getPlaysInGame(), $tournament->getMaxTeamsCount());
            $row['link'] = true;
            array_push($table['rows'], $row);
        }


        $new_tournament = new Tournament();
        $formadd = $this->make_form($new_tournament);

        // Zpracování add formuláře.
        $formadd->handleRequest($request);
        if ($formadd->isSubmitted() && $formadd->isValid()) {
            $this->tournamentRepository->save($new_tournament);
            $this->addFlash('success', 'Turnaj \'' . $new_tournament->getName() . '\' byl úspěšně přidán.');
            return $this->redirect($request->getUri());
        }

        return $this->render('pages/tables/index.html.twig', array('table_name' => $table_name, 'formadd' => $formadd->createView(), 'table' => $table));
    }
}