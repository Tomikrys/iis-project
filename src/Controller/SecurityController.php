<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
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
     * @Route("/logout/auto", name="app_logout_auto")
     */
    public function autologout() {
        $this->addFlash('warning', 'Byl jste automaticky odhlášen.');
        return $this->redirect($this->generateUrl('app_logout'));
    }

    /**
     * @Route("/logout/ondemnad", name="app_logout_ondemand")
     */
    public function ondemnadlogout() {
        $this->addFlash('success', 'Byl jste úspěšně odhlášen.');
        return $this->redirect($this->generateUrl('app_logout'));
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/register", name="app_register")
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
                'attr' => array('class' => 'btn btn btn-success mt-3')))
            ->getForm();

        // Zpracování add formuláře.
        $formadd->handleRequest($request);
        if ($formadd->isSubmitted() && $formadd->isValid()) {
            $user->setRoles(["ROLE_CAPTAIN"]);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $user->getPassword()
            ));
            $this->userRepository->save($user);
            $this->addFlash('success', 'Uživatel \'' . $user->getEmail() . '\' byl úspěšně přidán.');
            return $this->redirect($this->generateUrl('/login'));
        }

        return $this->render('security/register.html.twig', array('title' => $title, 'formadd' => $formadd->createView()));
    }

    /**
     * @Route("/users/upgrade/{id}", name="/users/upgrade", methods={"GET", "PATCH"})
     * @IsGranted("ROLE_ADMIN")
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
        $response->send();
    }

    /**
     * @Route("/users", name="/users", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
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
            $row['id'] = $user->getId();
            $row['data'] = array($user->getEmail(), $user->getRolesString());
            $row['link'] = false;
            $row['name'] = $user->getEmail();
            $row['isAdmin'] = $user->hasRole("ROLE_ADMIN");
            $row['rolesString'] = $user->getRolesString();
            array_push($table['rows'], $row);
        }

        return $this->render('pages/tables/pages/users.html.twig', array('table_name' => $table_name, 'table' => $table));
    }
}
