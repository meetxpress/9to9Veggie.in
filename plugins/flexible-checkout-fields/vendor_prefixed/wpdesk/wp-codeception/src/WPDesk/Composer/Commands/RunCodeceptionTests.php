<?php

namespace FcfVendor\WPDesk\Composer\Codeception\Commands;

use FcfVendor\Symfony\Component\Console\Input\InputArgument;
use FcfVendor\Symfony\Component\Console\Input\InputInterface;
use FcfVendor\Symfony\Component\Console\Output\OutputInterface;
/**
 * Codeception tests run command.
 *
 * @package WPDesk\Composer\Codeception\Commands
 */
class RunCodeceptionTests extends \FcfVendor\WPDesk\Composer\Codeception\Commands\BaseCommand
{
    const SINGLE = 'single';
    const FAST = 'fast';
    const WOOCOMMERCE_VERSION = 'woo_version';
    /**
     * Configure command.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('run-codeception-tests')->setDescription('Run codeception tests.')->setDefinition(array(new \FcfVendor\Symfony\Component\Console\Input\InputArgument(self::SINGLE, \FcfVendor\Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'Name of Single test to run.', 'all'), new \FcfVendor\Symfony\Component\Console\Input\InputArgument(self::FAST, \FcfVendor\Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'Fast tests - do not shutdown docker-compose.', 'slow'), new \FcfVendor\Symfony\Component\Console\Input\InputArgument(self::WOOCOMMERCE_VERSION, \FcfVendor\Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'WooCommerce version to install.', '')));
    }
    /**
     * Execute command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(\FcfVendor\Symfony\Component\Console\Input\InputInterface $input, \FcfVendor\Symfony\Component\Console\Output\OutputInterface $output)
    {
        $dockerComposeYaml = 'vendor/wpdesk/wp-codeception/docker/docker-compose.yaml';
        $singleTest = $input->getArgument(self::SINGLE);
        $fastTest = $input->getArgument(self::FAST);
        $wooVersion = $input->getArgument(self::WOOCOMMERCE_VERSION);
        $cache_dir = \sys_get_temp_dir() . '/codeception_cache';
        if (!\file_exists($cache_dir)) {
            \mkdir($cache_dir, 0777, \true);
        }
        \putenv('TMP_CACHE_DIR=' . $cache_dir);
        $codecept_param = ' --html --verbose ';
        $additionalParameters = ' -e CODECEPT_PARAM="' . $codecept_param . '" ';
        if (!empty($singleTest) && 'all' !== $singleTest) {
            $additionalParameters .= ' -e CODECEPT_PARAM="' . $codecept_param . ' acceptance ' . $singleTest . '" ';
        }
        if (!empty($wooVersion)) {
            $additionalParameters .= ' -e WOOCOMMERCE_VERSION="' . $wooVersion . '" ';
        }
        $runTestsCommand = 'docker-compose -f ' . $dockerComposeYaml . ' run ' . $additionalParameters . 'codecept';
        $output->writeln('Codeception command: ' . $runTestsCommand);
        $this->execAndOutput($runTestsCommand, $output);
        if (empty($fastTest) || self::FAST !== $fastTest) {
            $this->execAndOutput('docker-compose -f ' . $dockerComposeYaml . ' down -v', $output);
        }
    }
}
