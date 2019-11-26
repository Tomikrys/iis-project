<?php

namespace App\Controller;

use App\Entity\Tournament;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

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
        if ($formadd->isSubmitted() && $formadd->isValid()) {
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
                $row['link'] = false;
                $row['name'] = $user->getEmail();
                $row['isAdmin'] = $user->hasRole("ROLE_ADMIN");
                $row['rolesString'] = $user->getRolesString();
                array_push($table['rows'], $row);
            }
        }

        return $this->render('pages/tables/pages/users.html.twig', array('table_name' => $table_name, 'table' => $table));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/user/{id}", methods={"GET", "POST"})
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
        if ($formeditpassword->isSubmitted() && $formeditpassword->isValid()) {
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $user->getPassword()
            ));
            $this->userRepository->save($user);
            $this->addFlash('success', 'Uživateli \'' . $user->getEmail() . '\' bylo úspěšně změněno heslo.');
            return $this->redirect($request->getUri());
        }

        $admin = $team->getAdminString();

        return $this->render('pages/details/user.html.twig', array('title' => $title, 'user' => $user,
            'table_teams' => $table_teams, 'table_tournaments' => $table_tournaments, 'table_players' => $table_players,
            'formeditpassword' => $formeditpassword->createView(), "admin" => $admin));
    }
}
