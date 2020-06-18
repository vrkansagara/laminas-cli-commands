<?php

namespace Divix\Laminas\Cli\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Usage:
 * 
 * "vendor/bin/laminas-cli.bat" mvc:admin --module=<moduleName>
 */
class AdminPanelCommand extends AbstractCommand
{
    protected static $defaultName = 'mvc:admin';

    protected function configure()
    {
        $this
            ->setDescription('Creates a new Admin Panel feature.')
            ->setHelp('This command allows you to create a MVC Admin Panel');
        
        parent::configure();
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $section1 = $output->section();
        $section2 = $output->section();
        $section1->writeln('Start creating Admin Panel');
        
        $moduleName = $this->getModuleName($input, $output, 'rowset');
        
        /*$section1->writeln('Start creating new Model.');
        $this->generateModel($moduleName, 'User', $output, $properties);
        $section1->writeln('End creating new Model.');
        
        $section1->writeln('Start creating new Rowset.');
        $this->generateRowset($moduleName, 'User', $output, $properties);
        $section1->writeln('End creating new Rowset.');*/

        $this->createSimplePHP($moduleName, 'Module.php', $section2);
        $this->createSimplePHP($moduleName, 'ConfigProvider.php', $section2);
        $this->createSimplePHP($moduleName, 'Navigation/Service/AdminNavigationFactory.php', $section2);
        
        $this->createStaticView($moduleName, 'layout/admin.phtml', $section2);
        $this->createStaticView($moduleName, 'admin/admin/index.phtml', $section2);
        $this->createStaticView($moduleName, 'admin/_shared/footer.phtml', $section2);
        $this->createStaticView($moduleName, 'admin/_shared/menu.phtml', $section2);
        
        $this->createStaticController($moduleName, 'AbstractController.php', $section2);
        $this->createStaticController($moduleName, 'AdminController.php', $section2);
        $this->createStaticConfig($moduleName, 'config/module.config.php', $section2);
        /*$this->createStaticForm($moduleName, 'UsernameFieldset', $section2);
        $this->createUserRegisterForm($moduleName, $properties, $section2);
        
        
        $this->createLoginController($moduleName, $section2);
        $this->createLoginView($moduleName, $section2);
        $this->createUserController($moduleName, $section2);
        $this->createUserView($moduleName, $section2);*/
        
        if ($this->isJsonMode()) {
            $code = (json_encode([
                'global.php' => 
'\'session\' => [
    \'config\' => [
        \'class\' => \Laminas\Session\Config\SessionConfig::class,
        \'options\' => [
            \'name\' => \'session_name\',
        ],
    ],
    \'storage\' => \Laminas\Session\Storage\SessionArrayStorage::class,
    \'validators\' => [
        \Laminas\Session\Validator\RemoteAddr::class,
        \Laminas\Session\Validator\HttpUserAgent::class,
    ],
],'
            ]));
            $section2->writeln($code);
        }


        $section2->writeln('Done creating Admin Panel.');
        
        parent::postExecute($input, $output, $section1, $section2);

        return 0;
    }
    
    protected function createSimplePHP($moduleName, $filename, $section2)
    {
        $abstractContents = file_get_contents(__DIR__.'/Templates/AdminPanel/'.$filename);
        $abstractContents = str_replace("%module_name%", $moduleName, $abstractContents);
        
        if ($this->isJsonMode()) {
            $abstractContents = str_replace("<?php", '', $abstractContents);
            $code = (json_encode([$filename => $abstractContents]));
            $section2->writeln($code);
        }
        
        $this->storeControllerContents($filename, $moduleName, $abstractContents);
    }
    
    protected function createStaticController($moduleName, $filename, $section2)
    {
        $abstractContents = file_get_contents(__DIR__.'/Templates/AdminPanel/Controller/'.$filename);
        $abstractContents = str_replace("%module_name%", $moduleName, $abstractContents);
        
        if ($this->isJsonMode()) {
            $abstractContents = str_replace("<?php", '', $abstractContents);
            $code = (json_encode([$filename => $abstractContents]));
            $section2->writeln($code);
        }
        
        $this->storeControllerContents($filename, $moduleName, $abstractContents);
    }
    
    protected function createStaticConfig($moduleName, $filename, $section2)
    {
        $abstractContents = file_get_contents(__DIR__.'/Templates/AdminPanel/'.$filename);
        $abstractContents = str_replace("%module_name%", $moduleName, $abstractContents);
        
        if ($this->isJsonMode()) {
            $abstractContents = str_replace("<?php", '', $abstractContents);
            $code = (json_encode([$filename => $abstractContents]));
            $section2->writeln($code);
        }
        
        $this->storeControllerContents($filename, $moduleName, $abstractContents);
    }
    
    protected function createUserController($moduleName, $section2)
    {
        $abstractContents = file_get_contents(__DIR__.'/Templates/LoginRegister/UserController.php');
        $abstractContents = str_replace("%module_name%", $moduleName, $abstractContents);
        
        if ($this->isJsonMode()) {
            $abstractContents = str_replace("<?php", '', $abstractContents);
            $code = (json_encode(['UserController.php' => $abstractContents]));
            $section2->writeln($code);
        }
        
        $this->storeControllerContents('UserController.php', $moduleName, $abstractContents);
    }
    
    protected function createUserView($moduleName, $section2)
    {
        $abstractContents = file_get_contents(__DIR__.'/Templates/LoginRegister/View/user.phtml');
        
        //@TODO amend user properties 
        
        if ($this->isJsonMode()) {
            $code = (json_encode(['user/index.phtml' => $abstractContents]));
            $section2->writeln($code);
        }
        
        $this->storeViewContents('index.phtml', $moduleName, 'register', $abstractContents);
    }
    
    protected function createRegisterView($moduleName, $properties, $section2)
    {
        $abstractContents = file_get_contents(__DIR__.'/Templates/LoginRegister/View/register.phtml');
        $propertiesCode = '';
        
        foreach ($properties as $property) {
            $propertiesCode .= 
'                            echo $this->formRow($userForm->get(\''.$property.'\'));'.PHP_EOL;
        }
        $abstractContents = str_replace("%properties%", $propertiesCode, $abstractContents);
        
        if ($this->isJsonMode()) {
            $code = (json_encode(['register/index.phtml' => $abstractContents]));
            $section2->writeln($code);
        }
        
        $this->storeViewContents('index.phtml', $moduleName, 'register', $abstractContents);
    }
    
    protected function createHydrator($moduleName, $section2)
    {
        $abstractContents = file_get_contents(__DIR__.'/Templates/LoginRegister/Hydrator/UserFormHydrator.php');
        $abstractContents = str_replace("%module_name%", $moduleName, $abstractContents);
        
        if ($this->isJsonMode()) {
            $code = (json_encode(['UserFormHydrator.php' => $abstractContents]));
            $section2->writeln($code);
        }
        
        $this->storeViewContents('index.phtml', $moduleName, 'register', $abstractContents);
    }
    
    protected function createLoginView($moduleName, $section2)
    {
        $abstractContents = file_get_contents(__DIR__.'/Templates/LoginRegister/View/login.phtml');
        
        if ($this->isJsonMode()) {
            $code = (json_encode(['login/index.phtml' => $abstractContents]));
            $section2->writeln($code);
        }
        
        $this->storeViewContents('index.phtml', $moduleName, 'login', $abstractContents);
    }
    
    protected function createStaticView($moduleName, $filename, $section2)
    {
        $abstractContents = file_get_contents(__DIR__.'/Templates/AdminPanel/View/'.$filename);
        
        if ($this->isJsonMode()) {
            $code = (json_encode([$filename => $abstractContents]));
            $section2->writeln($code);
        }
        
        $this->storeViewContents($filename.'.php', $moduleName, 'admin', $abstractContents);
    }
    
    protected function createUserRegisterForm($moduleName, $properties, $section2)
    {
        $abstractContents = file_get_contents(__DIR__.'/Templates/LoginRegister/Form/UserRegisterForm.php');
        $abstractContents = str_replace("%module_name%", $moduleName, $abstractContents);
        
        $propertiesCode = '';
        
        foreach ($properties as $property) {
            $propertiesCode .= 
'$this->add([
    \'name\' => \''.$property.'\',
    \'type\' => \'text\',
    \'options\' => [
        \'label\' => \''.ucfirst($property).'\'
    ],
    \'attributes\' => [
        \'required\' => true
    ]
]);'.PHP_EOL;
        }
        $abstractContents = str_replace("%properties%", $propertiesCode, $abstractContents);
        
        if ($this->isJsonMode()) {
            $code = (json_encode(['UserRegisterForm.php' => $abstractContents]));
            $section2->writeln($code);
        }
        
        $this->storeFormContents('UserRegisterForm.php', $moduleName, $abstractContents);
    }
}