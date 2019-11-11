<?php


namespace App\Controller;

use App\Entity\Player;
use App\Entity\Team;
use App\Repository\PlayerRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlayerController extends AbstractController {
    /**
     * @var PlayerRepository
     */
    private $playerRepository;

    /**
     * @param PlayerRepository $playerRepository
     */
    public function __construct(PlayerRepository $playerRepository)
    {
        $this->playerRepository = $playerRepository;
    }

    /**
     * @param Request $request
     * @param $id
     * @Route("/players/delete/{id}", methods={"DELETE"})
     */
    public function delete(Request $request, $id) {
        $player = $this->getDoctrine()->getRepository(Player::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($player);
        $entityManager->flush();
        // vytvoření flash oznámení
        $this->addFlash('warning', 'Hráč \'' . $player->getName() . '\' byl odstraněn.');
        $response = new Response();
        $response->send();
    }


    /**
     * @param $player
     * @return \Symfony\Component\Form\FormInterface
     */
    public function make_form($player) {
        // vytvoření formuláře pro přidání záznamu
        $form = $this->createFormBuilder($player)
            ->add('name', TextType::class, array(
                'label' => 'Jméno',
                'attr'=> array('class' => 'form-control')
            ))
            ->add('is_girl', ChoiceType::class, array(
                'label' => 'Pohlaví',
                'choices'  => array(
                    'Muž' => false,
                    'Žena' => true
                ),
                'attr' => array('class' => 'custom-select')
            ))
            ->add('phone', TextType::class, array(
                'label' => 'Telefon',
                'attr'=> array('class' => 'form-control')
            ))
            ->add('email', TextType::class, array(
                'label' => 'Email',
                'attr'=> array('class' => 'form-control')
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Uložit',
                'attr' => array('class' => 'btn btn btn-success mt-3', 'data-dissmiss' => 'modal')) )
            ->getForm();
        return $form;
    }

    /**
     * @Route("/players/edit/{id}", methods={"GET", "POST"})
     */
    public function edit(Request $request, $id) {
        $title = "Editace hráče";
        $player = $this->getDoctrine()->getRepository(Player::class)->find($id);
        $formedit = $this->make_form($player);

        // Zpracování add formuláře.
        $formedit->handleRequest($request);
        if ($formedit->isSubmitted() && $formedit->isValid()) {
            $this->playerRepository->save($player);
            $this->addFlash('success', 'Hráč \'' . $player->getName() . '\' byl úspěšně editován.');
            return $this->redirect($this->generateUrl('/players'));
        }

        return $this->render('pages/tables/edit.html.twig', array('title' => $title, 'formedit' => $formedit->createView()));
    }

    /**
     * @Route("/players/detail/{id}")
     * @Method({"GET", "POST"})
     */
    public function show(Request $request, $id)
    {
        $player = $this->getDoctrine()->getRepository(Player::class)->find($id);
        $title = "Detail hráče '" . $player->getName() . "'";

        $table['name'] = "teams";
        $table['headers'] = array("Název");
        $table['rows'] = array();

        // naplnění struktury pro výpis tabulky
        $teams = $player->getTeams();
        $team = null;
        foreach($teams as $team){
            $row['id'] = $team->getId();
            $row['data'] = array($team->getName());
            array_push($table['rows'], $row);
        }

        $all_teams = $this->getDoctrine()->getRepository(Team::class)->findAll();
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
            $player->addTeam($team);
            $this->getDoctrine()->getManager()->persist($player);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Tým \'' . $team->getName() . '\' byl úspěšně  do turnaje \'' . $team->getName() . '\'.');
            return $this->redirect($request->getUri());
        }

        return $this->render('pages/details/player.html.twig', array('title' => $title, 'player' => $player,
            'table' => $table, 'formadd' => $formadd->createView()));
    }


    /**
     * @Route("/players", name="/players", methods={"GET", "POST"})
     */
    public function index(Request $request) {
        // promněnné pro výpis
        $table_name = "Tabulka hráčů";
        $table['name'] = "players";
        $table['headers'] = array("Jméno", "Pohlaví", "Telefon", "Email");
        $table['rows'] = array();

        // naplnění struktury pro výpis tabulky
        $players = $this->getDoctrine()->getRepository(Player::class)->findAll();
        $player = null;
        foreach($players as $player){
            $row['id'] = $player->getId();
            $row['link'] = true;
            $row['data'] = array($player->getName(), $player->getGender(), $player->getPhone(), $player->getEmail());
            array_push($table['rows'], $row);
        }


        $new_player = new Player();
        $formadd = $this->make_form($new_player);

        // Zpracování add formuláře.
        $formadd->handleRequest($request);
        if ($formadd->isSubmitted() && $formadd->isValid()) {
            $this->playerRepository->save($new_player);
            $this->addFlash('success', 'Hráč \'' . $new_player->getName() . '\' byl úspěšně přidán.');
            return $this->redirect($request->getUri());
        }
/*
        // TODO get id
        $player = $this->getDoctrine()->getRepository(Player::class)->find(29);
        $formedit = $this->make_form($player);

        // Zpracování add formuláře.
        $formedit->handleRequest($request);
        if ($formedit->isSubmitted() && $formedit->isValid()) {
            $this->playerRepository->save($player);
            $this->addFlash('success', 'Hráč \'' . $player->getName() . '\' byl úspěšně editován.');
            return $this->redirect($request->getUri());
        }
*/

        //   return $this->render('pages/tables/index.html.twig', array('table_name' => $table_name, 'formadd' => $formadd->createView(), 'formedit' => $formedit->createView(), 'table' => $table));
        return $this->render('pages/tables/index.html.twig', array('table_name' => $table_name, 'formadd' => $formadd->createView(), 'table' => $table));
    }

}