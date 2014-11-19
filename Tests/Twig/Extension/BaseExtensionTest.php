<?php
namespace Admingenerator\GeneratorBundle\Tests\Twig\Extension;

/**
 *
 * Base class to test extensions. Provide builtin functions to initialize
 * new Twig environment in order to assert a template and its rendered version
 * are coherent.
 *
 * @author Stéphane Escandell
 */
abstract class BaseExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Variables used for templates
     *
     * @var array
     */
    protected $twigVariables = array();

    /**
     * @var \Twig_Extension
     */
    protected $extension;

    /**
     * @return \Twig_Extension
     */
    abstract protected function getTestedExtension();

    /**
     * @return array
     */
    abstract protected function getTwigVariables();

    public function setUp()
    {
        $this->twigVariables = $this->getTwigVariables();
        $this->extension = $this->getTestedExtension();
    }

    protected function runTwigTests(array $templates, array $returns)
    {
        if (array_diff(array_keys($templates), array_keys($returns))) {
            throw new \LogicException(sprintf(
                'Error: invalid test case. Templates and returns keys mismatch: templates:[%s], returns:[%s] => [%s]',
                implode(', ', array_keys($templates)),
                implode(', ', array_keys($returns)),
                implode(', ', array_diff(array_keys($templates), array_keys($returns)))
            ));
        }
        $twig = $this->getEnvironment($templates);

        foreach ($templates as $name => $tpl) {
            $this->assertEquals(
                $returns[$name][0],
                $twig->loadTemplate($name)->render($this->twigVariables),
                $returns[$name][1]
            );
        }
    }

    protected function getEnvironment($templates, $options = array())
    {
        $twig = new \Twig_Environment(
            new \Twig_Loader_Array($templates),
            array_merge(
                array(
                    'debug' => true,
                    'cache' => false,
                    'autoescape' => false,
                ),
                $options
            )
        );
        $twig->addExtension($this->extension);

        return $twig;
    }
}
