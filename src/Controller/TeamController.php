<?php


namespace App\Controller;


use App\Entity\Player;
use App\Entity\Team;
use App\Repository\TeamRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TeamController
 * @package App\Controller
 *
 * controler pro správu týmu
 */
class TeamController extends AbstractController
{
    /**
     * @var TeamRepository
     */
    private $teamRepository;

    /**
     * TeamController constructor.
     * @param TeamRepository $teamRepository
     */
    public function __construct(TeamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }


    /**
     * @param $team
     * @return \Symfony\Component\Form\FormInterface
     *
     * funkce k vytvoření formuláře pro přidání a editaci týmu
     */
    public function make_form($team)
    {
        // vytvoření formuláře pro přidání záznamu
        $form = $this->createFormBuilder($team)
            ->add('name', TextType::class, array(
                'label' => 'Jméno',
                'attr' => array('class' => 'form-control')
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Uložit',
                'attr' => array('class' => 'btn btn btn-success mt-3 showloading', 'data-dissmiss' => 'modal')))
            ->getForm();
        return $form;
    }

    /**
     * @param Request $request
     * @param $id
     * @Route("/teams/delete/{id}", methods={"DELETE"})
     *
     * funkce k odstranění týmu z databáze
     */
    public function delete(Request $request, $id) {
        $team = $this->getDoctrine()->getRepository(Player::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($team);
        $entityManager->flush();
        // vytvoření flash oznámení
        $this->addFlash('warning', 'Tým \'' . $team->getName() . '\' byl odstraněn.');
        $response = new Response();
        $response->send();
    }


    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/teams/edit/{id}", methods={"GET", "POST"})
     *
     * funkce k editaci týmu
     */
    public function edit(Request $request, $id)
    {
        $title = "Editace týmu";
        $team = $this->getDoctrine()->getRepository(Team::class)->find($id);
        if (!($team)) {
            $this->addFlash('error', 'Turnaj s id \'' . $id . '\' neexistuje.');
            return $this->redirect("/teams");
        }
        if (!($this->getUser()->getEmail() == $team->getAdminString() or $this->getUser()->hasRole("ROLE_ADMIN"))) {
            $this->addFlash('error', 'Tým \'' . $team->getName() . '\' nemáte oprávnění upravovat.');
            return $this->redirect("/teams");
        }
        $formedit = $this->make_form($team);

        // Zpracování add formuláře.
        $formedit->handleRequest($request);
        if ($formedit->isSubmitted() && $formedit->isValid()) {
            $this->teamRepository->save($team);
            $this->addFlash('success', 'Tým \'' . $team->getName() . '\' byl úspěšně editován.');
            return $this->redirect($this->generateUrl('/teams'));
        }

        return $this->render('pages/tables/edit.html.twig', array('title' => $title, 'formedit' => $formedit->createView()));
    }


    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/teams/detail/{id}", methods={"GET", "POST"})
     *
     * funkce k zobrazení detailu hráče
     */
    public function show(Request $request, $id)
    {
        $team = $this->getDoctrine()->getRepository(Team::class)->find($id);
        if (!($team)) {
            $this->addFlash('error', 'Turnaj s id \'' . $id . '\' neexistuje.');
            return $this->redirect("/teams");
        }
        $title = "Detail týmu '" . $team->getName() . "'";

        $table['name'] = "players";
        $table['headers'] = array("Jméno", "Pohlaví", "Telefon", "Email");
        $table['rows'] = array();
        $admin = $team->getAdminString();

        // naplnění struktury pro výpis tabulky
        // TODO změnit na správenj tým
        $players = $team->getPlayers();
        $player = null;
        foreach($players as $player){
            $row['id'] = $player->getId();
            $row['data'] = array($player->getName(), $player->getGender(), $player->getPhone(), $player->getEmail());
            array_push($table['rows'], $row);
        }

        //$all_players = $this->getDoctrine()->getRepository(Player::class)->findAll();
        $all_players = $team->getAdmin()->getPlayers();
        $form_players = null;
        foreach ($all_players as $player) {
            // slouží k výpisu jen hráču, co ještě nejsou v týmu
            if (!$players->contains($player)) {
                $form_players[$player->getName()] = $player->getId();
            }
        }

        $add_player = new Player();
        // Dělám tady trochu bordel, sry, do name si uložím ID toho hráča, protože nemám setID, tak to dělám přes setName
        // Dole to pak použiju k vyhledání toho hráča, co se má uložit
        $formadd = $this->createFormBuilder($add_player)
            ->add('name', ChoiceType::class, array(
                'choices'  => $form_players,
                'attr' => array('class' => 'custom-select'),
                'placeholder' => " ",
                'label' => 'Dostupní hráči' ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Uložit',
                'attr' => array('class' => 'btn btn btn-success mt-3 showloading', 'data-dissmiss' => 'modal')) )
            ->getForm();

        // Zpracování add formuláře.
        $formadd->handleRequest($request);
        if ($formadd->isSubmitted() && $formadd->isValid()) {
            // sic je tady getName, tak do name jsem výše uložil ID toho hráča, takže se hledá podle ID, sorry.
            $player = $this->getDoctrine()->getRepository(Player::class)->find($add_player->getName());
            $team->addPlayer($player);
            $this->getDoctrine()->getManager()->persist($team);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Hráč \'' . $player->getName() . '\' byl úspěšně  do týmu \'' . $team->getName() . '\'.');
            return $this->redirect($request->getUri());
        }

        return $this->render('pages/details/team.html.twig', array('title' => $title, 'team' => $team,
            'table' => $table, 'formadd' => $formadd->createView(), 'admin' => $admin));
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/teams", name="/teams", methods={"GET", "POST"})
     *
     * funkce k zobrazení všech hráčů
     */
    public function index(Request $request)
    {
        // promněnné pro výpis
        $table_name = "Tabulka týmů";
        $table['name'] = "teams";
        $table['headers'] = array("Název", "Počet hráčů");
        $table['rows'] = array();

        // naplnění struktury pro výpis tabulky
        $teams = $this->getDoctrine()->getRepository(Team::class)->findAll();
        $team = null;
        foreach ($teams as $team) {
            $row['id'] = $team->getId();
            $row['data'] = array($team->getName(), $team->getPlayersCount());
            $row['link'] = true;
            $row['name'] = $team->getName();
            $row['playersCount'] = $team->getPlayersCount();
            $row["admin"] = $team->getAdminString();
            array_push($table['rows'], $row); 
        }


        $new_team = new Team();
        $formadd = $this->make_form($new_team);

        // Zpracování add formuláře.
        $formadd->handleRequest($request);
        if ($formadd->isSubmitted() && $formadd->isValid()) {
            $new_team->setAdmin($this->getUser());
            $this->teamRepository->save($new_team);
            $this->addFlash('success', 'Tým \'' . $new_team->getName() . '\' byl úspěšně přidán.');
            return $this->redirect($request->getUri());
        }

        return $this->render('pages/tables/pages/teams.html.twig', array('table_name' => $table_name, 'formadd' => $formadd->createView(), 'table' => $table));
    }
}