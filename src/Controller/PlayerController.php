<?php


namespace App\Controller;

use App\Entity\Player;
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
     * @Route("/players/edit/{id}")
     * @Method({"GET", "POST"})
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
     * @Route("/players", name="/players")
     * @Method({"GET", "POST"})
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
            $row['link'] = false;
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