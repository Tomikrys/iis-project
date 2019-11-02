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
        $this->addFlash('warning', 'Hráč byl odstraněn.');
        $response = new Response();
        $response->send();
    }

    /**
     * @Route("/players")
     * @Method({"GET", "POST"})
     */
    public function index(Request $request) {
        // promněnné pro výpis
        $table_name = "Tabulka hráčů";
        $table['name'] = "players";
        $table['headers'] = array("Jmeno", "Pohlavi", "Telefon", "Email");
        $table['rows'] = array();

        // naplnění struktury pro výpis tabulky
        $players = $this->getDoctrine()->getRepository(Player::class)->findAll();
        $player = null;
        foreach($players as $player){
            $row['id'] = $player->getId();
            $row['data'] = array($player->getName(), $player->getGender(), $player->getPhone(), $player->getEmail());
            array_push($table['rows'], $row);
        }

        $new_player = new Player();
        // vytvoření formuláře pro přidání záznamu
        $formadd = $this->createFormBuilder($new_player)
            ->add('name', TextType::class, array( 'attr'=> array(
                'class' => 'form-control',
                'label' => 'Jméno') ))
            ->add('is_girl', ChoiceType::class, array(
                'choices'  => [
                    'Muž' => false,
                    'Žena' => true
                ],
                'attr' => array('class' => 'custom-select'),
                'label' => 'Pohlaví' ))
            ->add('phone', TextType::class, array( 'attr'=> array(
                'class' => 'form-control',
                'label' => 'Telefon') ))
            ->add('email', TextType::class, array( 'attr'=> array(
                'class' => 'form-control',
                'label' => 'Email') ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Přidat hráče',
                'attr' => array('class' => 'btn btn btn-success mt-3', 'data-dissmiss' => 'modal')) )
            ->getForm();

        // Zpracování editačního formuláře.
        $formadd->handleRequest($request);
        if ($formadd->isSubmitted() && $formadd->isValid()) {
            $this->playerRepository->save($new_player);
            $this->addFlash('success', 'Hráč byl úspěšně uložen.');
            return $this->render('pages/tables/index.html.twig', array('table_name' => $table_name, 'formadd' => $formadd->createView(), 'table' => $table));
        }

        return $this->render('pages/tables/index.html.twig', array('table_name' => $table_name, 'formadd' => $formadd->createView(), 'table' => $table));
    }

}