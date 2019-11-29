<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\Team;
use App\Entity\Tournament;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * UserRepository constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     *
     * funkce k přihlášení uživatele
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute($this->generateUrl("/tournaments"));
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }


//    TODO neháže to ty flashe, neívm proč

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/logout/auto", name="app_logout_auto")
     *
     * funkce co vypíše info, že byl uživatel automaticky odhlášen a odhlásí jej
     */
    public function autologout() {
        $this->addFlash('warning', 'Byl jste automaticky odhlášen.');
        return $this->redirect($this->generateUrl('app_logout'));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/logout/ondemnad", name="app_logout_ondemand")
     *
     * funkce připraví hlášení o odhlášení a odhlásí uživatele
     */
    public function ondemnadlogout() {
        $this->addFlash('success', 'Byl jste úspěšně odhlášen.');
        return $this->redirect($this->generateUrl('app_logout'));
    }

    /**
     * @throws \Exception
     * @Route("/logout", name="app_logout")
     *
     * odhlášení uživatlee
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/register", name="app_register")
     *
     * funkce k registraci uživateley
     */
    public function register(Request $request) {
        $title = "Resgistrace uživatele";
        $user = new User();

        $formadd = $this->createFormBuilder($user)
            ->add('email', EmailType::class, array(
                'label' => 'Email',
                'attr' => array('class' => 'form-control')
            ))
            ->add('password', PasswordType::class, array(
                'label' => 'Heslo',
                'attr' => array('class' => 'form-control')
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Registrovat',
                'attr' => array('class' => 'btn btn btn-success mt-3 showloading')))
            ->getForm();

        // Zpracování add formuláře.
        $formadd->handleRequest($request);
        if ($formadd->isSubmitted()) {
            if ($formadd->isValid()) {
                $user->setRoles(["ROLE_CAPTAIN"]);
                $user->setPassword($this->passwordEncoder->encodePassword(
                    $user,
                    $user->getPassword()
                ));
                try {
                    $this->userRepository->save($user);
                } catch (UniqueConstraintViolationException $e) {
                    $this->addFlash('error', 'Uživatel s emailovou adresou \'' . $user->getEmail() . '\' již existuje.');
                    return $this->redirect($this->generateUrl('app_register'));
                }
                $this->addFlash('success', 'Uživatel \'' . $user->getEmail() . '\' byl úspěšně přidán.');
                $this->addFlash('info', 'Prosím, přihlaste se.');
                return $this->redirect($this->generateUrl('app_login'));
            } else {
                $this->addFlash('error', 'Uživatel nemohl být přidán, špatně vyplněný formulář.');
            }
        }
        return $this->render('security/register.html.twig', array('title' => $title, 'formadd' => $formadd->createView()));
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @Route("/users/upgrade/{id}", name="/users/upgrade", methods={"PATCH"})
     * @IsGranted("ROLE_ADMIN")
     *
     * funkce k přidání administrátorských práv uživateli
     */
    public function make_admin(Request $request, $id) {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $userRoles = $user->getRoles();
        array_push($userRoles, "ROLE_ADMIN");
        $user->setRoles($userRoles);
        $entityManager->flush();
        // vytvoření flash oznámení
        $this->addFlash('warning', 'Uživatel \'' . $user->getEmail() . '\' byl povýšen na admina.');
        $response = new Response();
        return $response;
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @Route("/users/degrade/{id}", name="/users/downgrade", methods={"PATCH"})
     * @IsGranted("ROLE_ADMIN")
     *
     * funkce k odebrání administrátorských práv uživateli
     */
    public function remove_admin(Request $request, $id) {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $userRoles = $user->getRoles();
        $index = array_search('ROLE_ADMIN',$userRoles);
        if($index !== FALSE){
            unset($userRoles[$index]);
        }
        $user->setRoles($userRoles);
        $entityManager->flush();
        // vytvoření flash oznámení
        $this->addFlash('warning', 'Uživatel \'' . $user->getEmail() . '\' byl degradován na prostého uživatele.');
        $response = new Response();
        return $response;
    }


    /**
     * @param Request $request
     * @return Response
     * @Route("/users", name="/users", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     *
     * funkce k vypsání všech uživatelů
     */
    public function index(Request $request)
    {
        // promněnné pro výpis
        $table_name = "Tabulka uživatelů";
        $table['name'] = "users";
        $table['headers'] = array("Email", "Seznam rolí");
        $table['rows'] = array();

        // naplnění struktury pro výpis tabulky
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $user = null;
        foreach ($users as $user) {
            if ($user->getId() != $this->getUser()->getId()) {
                $row['id'] = $user->getId();
                $row['data'] = array($user->getEmail(), $user->getRolesString());
                $row['link'] = true;
                $row['name'] = $user->getEmail();
                $row['isAdmin'] = $user->hasRole("ROLE_ADMIN");
                $row['rolesString'] = $user->getRolesString();
                array_push($table['rows'], $row);
            }
        }

        return $this->render('pages/tables/pages/users.html.twig', array('table_name' => $table_name, 'table' => $table));
    }


    /**
     * @param $tournament
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Exception
     *
     * funkce pro tvormu formuláře k editaci či přidání turnaje
     */
    public function make_tournament_form($tournament)
    {
        // vytvoření formuláře pro přidání záznamu
        $tournament_form = $this->createFormBuilder($tournament)
            ->setAction('/users/tournament_add')
            ->add('name', TextType::class, array(
                'label' => 'Název',
                'attr' => array('class' => 'form-control')
            ))
            ->add('date', DateType::class, array(
                'label' => 'Datum',
                'widget' => 'choice',
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
        return $tournament_form;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     * @Route("/users/tournament_add", name="/users/tournament_add", methods={"GET", "POST"})
     */
    public function handle_tournament_form(Request $request) {
        // Zpracování add formuláře.
        $tournament = new Tournament();
        $tournament_form = $this->make_tournament_form($tournament);
        $tournament_form->handleRequest($request);
        if ($tournament_form->isSubmitted()) {
            if ($tournament_form->isValid()) {
                $tournament->setAdmin($this->getUser());
                $this->getDoctrine()->getManager()->persist($tournament);
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'Turnaj \'' . $tournament->getName() . '\' byl úspěšně vytvořen.');
                return $this->redirect('/bring_me_back');
            } else {
                $this->addFlash('error', 'Turnaj nemohl být přidán, špatně vyplněný formulář.');
            }
        }
        return new Response();
    }

    /**
     * @param $team
     * @return \Symfony\Component\Form\FormInterface
     *
     * funkce k vytvoření formuláře pro přidání a editaci týmu
     */
    public function make_team_form($team)
    {
        // vytvoření formuláře pro přidání záznamu
        $team_form = $this->createFormBuilder($team)
            ->setAction('/users/team_add')
            ->add('name', TextType::class, array(
                'label' => 'Jméno',
                'attr' => array('class' => 'form-control')
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Uložit',
                'attr' => array('class' => 'btn btn btn-success mt-3 showloading', 'data-dissmiss' => 'modal')))
            ->getForm();
        return $team_form;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/users/team_add", name="/users/team_add", methods={"GET", "POST"})
     */
    public function handle_team_form(Request $request) {
        // Zpracování add formuláře.
        $team = new Team();
        $team_form = $this->make_team_form($team);
        $team_form->handleRequest($request);
        if ($team_form->isSubmitted()) {
            if ($team_form->isValid()) {
                $team->setAdmin($this->getUser());
                $this->getDoctrine()->getManager()->persist($team);
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'Tým \'' . $team->getName() . '\' byl úspěšně vytvořen.');
                return $this->redirect("/bring_me_back");
            } else {
                $this->addFlash('error', 'Tým nemohl být přidán, špatně vyplněný formulář.');
            }
        }
        return new Response();
    }

    /**
     * @param $player
     * @return \Symfony\Component\Form\FormInterface
     *
     * funkce vytvoří formulář k editaci a k přidání hráče
     */
    public function make_player_form($player) {
        // vytvoření formuláře pro přidání záznamu
        $player_form = $this->createFormBuilder($player)
            ->setAction('/users/player_add')
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
                'placeholder' => " ",
                'attr' => array('class' => 'custom-select')
            ))
            ->add('phone', TextType::class, array(
                'label' => 'Telefon',
                'required'   => false,
                'attr'=> array('class' => 'form-control')
            ))
            ->add('email', TextType::class, array(
                'label' => 'Email',
                'required'   => false,
                'attr'=> array('class' => 'form-control')
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Uložit',
                'attr' => array('class' => 'btn btn btn-success mt-3 showloading', 'data-dissmiss' => 'modal')) )
            ->getForm();
        return $player_form;
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/users/player_add", name="/users/player_add", methods={"GET", "POST"})
     */
    public function handle_player_form(Request $request) {
        // Zpracování add formuláře.
        $player = new Player();
        $player_form = $this->make_player_form($player);
        $player_form->handleRequest($request);
            if ($player_form->isSubmitted()) {
                if ($player_form->isValid()) {
                    $player->setAdmin($this->getUser());
                    $this->getDoctrine()->getManager()->persist($player);
                    $this->getDoctrine()->getManager()->flush();
                    $this->addFlash('success', 'Hráč \'' . $player->getName() . '\' byl úspěšně vytvořen.');
                    return $this->redirect("/bring_me_back");
                } else {
                    $this->addFlash('error', 'Hráč nemohl být přidán, špatně vyplněný formulář.');
                }
            }
        return new Response();
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     * @Route("/users/detail/{id}", name="/users/detail", methods={"GET", "POST"})
     *
     * funkce k zobrazení detailu uživatele
     */
    public function show(Request $request, $id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $title = "Detail uživatele '" . $user->getEmail() . "'";

        $table_teams['name'] = "teams";
        $table_teams['headers'] = array("Název");
        $table_teams['rows'] = array();

        // naplnění struktury pro výpis tabulky
        // TODO změnit na správenj tým
        $teams = $user->getTeams();
        $team = null;
        foreach($teams as $team){
            $row['id'] = $team->getId();
            $row['data'] = array($team->getName());
            $row['link'] = true;
            $row["admin"] = $team->getAdminString();
            array_push($table_teams['rows'], $row);
        }

        $table_tournaments['name'] = "tournaments";
        $table_tournaments['headers'] = array("Název", "Datum", "Cena", "Organizátor", "Počer her na set", "Maximální počet týmů");
        $table_tournaments['rows'] = array();

        // naplnění struktury pro výpis tabulky
        $tournaments = $user->getTournaments();
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

            array_push($table_tournaments['rows'], $row);
        }

        $table_players['name'] = "players";
        $table_players['headers'] = array("Jméno", "Pohlaví", "Telefon", "Email");
        $table_players['rows'] = array();

        // naplnění struktury pro výpis tabulky
        // TODO změnit na správenj tým
        $players = $user->getPlayers();
        $player = null;
        foreach($players as $player){
            $row['id'] = $player->getId();
            $row['link'] = true;
            $row["admin"] = $player->getAdminString();
            $row['data'] = array($player->getName(), $player->getGender(), $player->getPhone(), $player->getEmail());
            array_push($table_players['rows'], $row);
        }

        $formeditpassword = $this->createFormBuilder($user)
            ->add('password', PasswordType::class, array(
                'label' => 'Nové heslo',
                'attr' => array('class' => 'form-control')
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Uložit',
                'attr' => array('class' => 'btn btn btn-success mt-3 showloading', 'data-dissmiss' => 'modal')))
            ->getForm();

        // Zpracování add formuláře.
        $formeditpassword->handleRequest($request);
        if ($formeditpassword->isSubmitted()) {
            if ($formeditpassword->isValid()) {
                $user->setPassword($this->passwordEncoder->encodePassword(
                    $user,
                    $user->getPassword()
                ));
                $this->userRepository->save($user);
                $this->addFlash('success', 'Uživateli \'' . $user->getEmail() . '\' bylo úspěšně změněno heslo.');
                return $this->redirect($request->getUri());
            } else {
                $this->addFlash('error', 'Heslo nemohlo být změneno, špatně vyplněný formulář.');
            }
        }

        $admin = null;
        if ($team != null)
            $admin = $team->getAdminString();

        // modální okna pro přidávání do tabulek
        $tournament = new Tournament();
        $formadd_tournaments = $this->make_tournament_form($tournament);

        $team = new Team();
        $formadd_teams = $this->make_team_form($team);

        $player = new Player();
        $formadd_players = $this->make_player_form($player);

        return $this->render('pages/details/user.html.twig', array('title' => $title, 'user' => $user,
            'table_teams' => $table_teams, 'table_tournaments' => $table_tournaments, 'table_players' => $table_players,
            'formadd_tournaments' => $formadd_tournaments->createView(), 'formadd_teams' => $formadd_teams->createView(),
            'formadd_players' => $formadd_players->createView(),
            'formeditpassword' => $formeditpassword->createView(), "admin" => $admin));
    }
}
