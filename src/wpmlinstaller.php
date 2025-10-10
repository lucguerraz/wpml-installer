<?php

namespace lucguerraz\wpmlinstaller;

use Composer\Composer;
use Composer\Factory;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PreFileDownloadEvent;

use Dotenv\Dotenv;

use lucguerraz\wpmlinstaller\exceptions\missingkeyexception;
use lucguerraz\wpmlinstaller\exceptions\faultykeyexception;

class wpmlinstaller implements PluginInterface, EventSubscriberInterface
{
    /**
     * @access protected
     * @var    Composer
     */
    protected $composer;

    /**
     * @access protected
     * @var    IOInterface
     */
    protected $io;

    /**
     * The function that is called when the plugin is activated
     *
     * Makes composer and io available to this class
     * It finds the composer baseDir and loads the .env file
     *
     * @access public
     * @param  Composer    $composer The composer object
     * @param  IOInterface $io       Not used
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;

        $composerFile = Factory::getComposerFile();
        $composerFilePath = realpath($composerFile);
        $baseDir = dirname($composerFilePath);

        $dotenv = Dotenv::createImmutable($baseDir);
        $dotenv->safeLoad();
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
    }

    /**
     * Subscribe this Plugin to relevant Events
     *
     * Pre Download: The user id, sub key and time need to be added to the url
     *               (will not show up in composer.lock)
     *
     * @access public
     * @return array An array of events that the plugin subscribes to
     * @static
     */
    public static function getSubscribedEvents()
    {
        return array(
            PluginEvents::PRE_FILE_DOWNLOAD => array(
                array('onPreFileDownload', 0)
            ),
        );
    }

    /**
     * Checks if the download is an WPML package, if so it inserts user_id, subscription_key and t (time) into the URL
     *
     * The key is not added to the package because it would show up in the
     * composer.lock file in this case. ProcessedUrl is used to
     * swap out the WPML url with a url that contains the user id, sub key and time.
     *
     * @access public
     * @param  PreFileDownloadEvent $event The event that called this method
     * @throws missingkeyexception
     * @throws faultykeyexception
     */
    public function onPreFileDownload(PreFileDownloadEvent $event)
    {
        $packageUrl = $event->getProcessedUrl();

        $host = parse_url($event->getProcessedUrl(), PHP_URL_HOST);

        if ($host !== 'wpml.org') {
            return;
        }

        $event->setProcessedUrl($this->buildDownloadUrl($packageUrl));
    }

    /**
     * Build the new WPML download URL
     *
     * @access protected
     * @param  string $packageUrl
     * @return string
     * @throws missingkeyexception
     */
    private function buildDownloadUrl(string $packageUrl): string
    {
        $url_parts = parse_url($packageUrl);
        parse_str($url_parts['query'], $url_query_parts);

        $wpml_user_id = trim($this->getParameter('WPML_USER_ID'));
        if (!is_numeric($wpml_user_id) || strlen($wpml_user_id) < 4 || strlen($wpml_user_id) > 10) {
            throw new faultykeyexception('WPML_USER_ID');
        }

        $wpml_sub_key = trim($this->getParameter('WPML_SUBSCRIPTION_KEY'));
        if (preg_match("/^[a-zA-Z0-9$.]{32}$/", $wpml_sub_key) !== 1) {
            throw new faultykeyexception('WPML_SUBSCRIPTION_KEY');
        }

        $complete_url_query_parts = [
            'download' => $url_query_parts['download'],
            'user_id' => $wpml_user_id,
            'subscription_key' => $wpml_sub_key,
            't' => time(),
            'version' => $url_query_parts['version']
        ];

        $complete_url_query = http_build_query($complete_url_query_parts);

        $packageUrl = $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . '?' . $complete_url_query;

        return $packageUrl;
    }


    /**
     * Get the WPML env variables
     *
     * @access protected
     * @return string The key from the environment
     * @throws missingkeyexception
     */
    private function getParameter(string $key)
    {
        if (isset($_ENV[$key])) {
            $value_dotenv = $_ENV[$key];
            if (!empty($value_dotenv)) {
                return $value_dotenv;
            }
        }

        $value_sysenv = getenv($key);
        if (!empty($value_sysenv)) {
            return $value_sysenv;
        }

        throw new missingkeyexception($key);
    }
}
