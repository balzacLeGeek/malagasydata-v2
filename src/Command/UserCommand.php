<?php
namespace App\Command;

use App\Entity\User;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserCommand extends Command
{
	/**
	* @var UserManager $userManager
	*/
	private $userManager;

	public function __construct(UserManager $userManager)
	{
		parent::__construct();

		$this->userManager = $userManager;
	}

    protected function configure()
	{
		$this
			->setName('app:create:user')
			->setDescription('Create new User"')
			->setHelp('Create new User with command => php bin/console app:create:user')
			->setDefinition([
                new InputArgument('lastname', InputArgument::REQUIRED, "User lastname"),
                new InputArgument('firstname', InputArgument::REQUIRED, "User fistname"),
                new InputArgument('email', InputArgument::REQUIRED, "User email"),
                new InputArgument('password', InputArgument::REQUIRED, "User password"),
                new InputArgument('role', InputArgument::REQUIRED, "User role"),
			])
		;
	}

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $output->writeln(["Let's create a new user",]);
        $lastname 	= $input->getArgument("lastname");
        $firstname 	= $input->getArgument("firstname");
        $email 		= $input->getArgument("email");
        $password 	= $input->getArgument("password");
        $role 		= strtolower($input->getArgument("role"));

        switch ($role) {
            case 'admin':
                $roleUser = User::ADMIN;
				break;

            case 'user':
                $roleUser = User::USER;
				break;
				
			default:
                $roleUser = User::USER;
				break;
        }

		/** @var UserRepository $userRepository */
        $userRepository = $this->userManager->getRepository(User::class);
        
        $userByUsername = $userRepository->findOneByUsername($email);
        $userByEmail 	= $userRepository->findOneByEmail($email);

        if (!$userByUsername && !$userByEmail) {
            $user = new User();

			$user
				->setFirstName($firstname)
                ->setLastname($lastname)
                ->setUsername($email)
                ->setEmail($email)
                ->setRoles([$roleUser])
				->setStatus(User::USER_ENABLE);

			$registredUser = $this->userManager->registerUser($user, $password);

			if ($registredUser) {
				$io->success("Great!!! User successfully created");

				$io->table(['Id', 'Email', 'Password', 'Role', 'Status'], [
					0 => [
						$user->getId(), $email, $password, $roleUser, $registredUser->getStatus(),
				]]);

				return 0;
			}
        }
        else {
            $io->warning("Sorry!!! User with the same username or email already exists");
        }
    }

    protected function interact(InputInterface $input, OutputInterface $output)
	{
		$questions = [];

		if (!$input->getArgument('lastname')) {
			$question = new Question("User lastname: ");
			$question->setValidator(function ($lastname) {
				if (empty($lastname)) {
					throw new \Exception('This field is required');
				}

				return $lastname;
			});

			$questions['lastname'] = $question;
		}

		if (!$input->getArgument('firstname')) {
			$question = new Question("User fistname: ");
			$question->setValidator(function ($firstname) {
				if (empty($firstname)) {
					throw new \Exception('This field is required');
				}

				return $firstname;
			});

			$questions['firstname'] = $question;
		}

		if (!$input->getArgument('email')) {
			$question = new Question("User email: ");
			$question->setValidator(function ($email) {
				if (empty($email)) {
					throw new \Exception('This field is required');
				}

				return $email;
			});

			$questions['email'] = $question;
		}

		if (!$input->getArgument('password')) {
			$question = new Question("User password: ");
			$question->setValidator(function ($password) {
				if (empty($password)) {
					throw new \Exception('This field is required');
				}

				return $password;
			});

			$questions['password'] = $question;
		}

		if (!$input->getArgument('role')) {
			$question = new Question("User role: ");
			$question->setValidator(function ($role) {
				if (empty($role)) {
					throw new \Exception('This field is required');
				} 
				
				if (!in_array($role, ['admin', 'user'])) {
					throw new \Exception('The allowed value is "admin" or "user"');
				}

				return $role;
			});

			$questions['role'] = $question;
		}

		foreach ($questions as $name => $question) {
			$answer = $this->getHelper('question')->ask($input, $output, $question);
			$input->setArgument($name, $answer);
		}
	}
}
