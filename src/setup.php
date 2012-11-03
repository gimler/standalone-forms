<?php

use Symfony\Component\Validator\Validation;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;

// Overwrite this with your own secret
define('CSRF_SECRET', 'c2ioeEU1n48QF2WsHGWd2HmiuUUT6dxr');

define('VENDOR_DIR', realpath(__DIR__ . '/../vendor'));
define('VENDOR_FORM_DIR', VENDOR_DIR . '/symfony/form/Symfony/Component/Form');
define('VENDOR_VALIDATOR_DIR', VENDOR_DIR . '/symfony/form/Symfony/Component/Validator');
define('VENDOR_TWIG_BRIDGE_DIR', VENDOR_DIR . '/symfony/twig-bridge/Symfony/Bridge/Twig');
define('VIEWS_DIR', realpath(__DIR__ . '/../views'));
define('CACHE_DIR', realpath(__DIR__ . '/../cache'));

// Set up the CSRF provider
$csrfProvider = new DefaultCsrfProvider(CSRF_SECRET);

// Set up the Validator component
$validator = Validation::createValidator();

// Set up the Translation component
$translator = new Translator('en');
$translator->addLoader('xlf', new XliffFileLoader());
$translator->addResource('xlf', VENDOR_FORM_DIR . '/Resources/translations/validators.en.xlf', 'en', 'validators');
$translator->addResource('xlf', VENDOR_VALIDATOR_DIR . '/Resources/translations/validators.en.xlf', 'en', 'validators');

// Set up Twig
$loader = new Twig_Loader_Filesystem(array(
    VIEWS_DIR,
    VENDOR_TWIG_BRIDGE_DIR . '/Resources/views/Form',
));
$twigFormEngine = new TwigRendererEngine(array('form_div_layout.html.twig'));
$twig = new Twig_Environment($loader, array(
    'cache' => CACHE_DIR,
));
$twig->addExtension(new TranslationExtension($translator));
$twig->addExtension(new FormExtension(new TwigRenderer($twigFormEngine, $csrfProvider)));
$twigFormEngine->setEnvironment($twig);

// Set up the Form component
$formFactory = Forms::createFormFactoryBuilder()
    ->addExtension(new CsrfExtension($csrfProvider))
    ->addExtension(new ValidatorExtension($validator))
    ->getFormFactory();
