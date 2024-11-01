<?php

define('TBB_COMPOSER_SLUG', 'tbb_composer');

require_once dirname(__FILE__) . '/class.tgm-plugin-activation.php';
require_once dirname(__FILE__) . '/class.array-packages-table.php';


use Composer\Factory;
use Composer\Composer;
use Composer\Installer;
use Composer\IO\NullIO;
use Composer\Repository\CompositeRepository;
use Composer\Repository\PlatformRepository;
use Composer\Repository\RepositoryInterface;
use Composer\Json\JsonFile;
use Composer\Repository\InstalledFilesystemRepository;

class TBBComposer
{
    
    function __construct()
    {
        $this->init();
    }
    
    public function addSettingsLink($links)
    {
        
        if (!class_exists('ReduxFramework')) {
            return $links;
        } else {
            $settings_link = '<a href="options-general.php?page=' . TBB_COMPOSER_SLUG . '_settings' . '">Settings</a>';
            array_unshift($links, $settings_link);
            return $links;
        }
    }
    
    private function init()
    {
        
        
        add_action('tgmpa_register', array(
            $this,
            'check'
        ));
        
        
        
        add_action('plugins_loaded', array(
            $this,
            'initSettings'
        ), 10);
        
        add_filter('plugin_action_links_' . TBB_COMPOSER_PLUGIN_BASENAME, array(
            $this,
            'addSettingsLink'
        ));
    }
    
    
    
    public function initSettings()
    {
        if (!class_exists('ReduxFramework')) {
            return;
        }
        $this->reduxFramework = new ReduxFramework($this->getSections(), $this->getArgs());
        
        global ${TBB_COMPOSER_SLUG . '_settings'};
        
        $this->settings = ${TBB_COMPOSER_SLUG . '_settings'};
        
        $this->settings['composer_json_path'] = str_replace('%%ABSPATH%%', ABSPATH, $this->settings['composer_json_path']);
        
        $this->settings['composer_phar_path'] = str_replace('%%ABSPATH%%', ABSPATH, $this->settings['composer_phar_path']);
        
        $this->settings['composer_home_path'] = str_replace('%%ABSPATH%%', ABSPATH, $this->settings['composer_home_path']);
        
        if (!@include_once 'phar://' . $this->settings['composer_phar_path'] . '/src/bootstrap.php') {
            add_action('admin_notices', array(
                $this,
                'addPharErrorMessage'
            ));
        } else {
            putenv('COMPOSER_HOME=' . $this->settings['composer_home_path']);
            
            add_action('admin_menu', array(
                $this,
                'addMenu'
            ));
        }
    }
    
    private function getArgs()
    {
        return array(
            // TYPICAL -> Change these values as you need/desire
            'opt_name' => TBB_COMPOSER_SLUG . '_settings', // This is where your data is stored in the database and also becomes your global variable name.
            'display_name' => __("The Blackest Box's Composer", TBB_COMPOSER_SLUG), // Name that appears at the top of your panel
            'display_version' => TBB_COMPOSER_VERSION, // Version that appears at the top of your panel
            'menu_type' => 'submenu', //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
            'allow_sub_menu' => true, // Show the sections below the admin menu item or not
            'menu_title' => __('Composer', TBB_COMPOSER_SLUG),
            'page_title' => __("The Blackest Box's Composer", TBB_COMPOSER_SLUG),
            'admin_bar' => false, // Show the panel pages on the admin bar
            'global_variable' => '', // Set a different name for your global variable other than the opt_name
            'dev_mode' => false, // Show the time the page took to load, etc
            'customizer' => true, // Enable basic customizer support
            
            // OPTIONAL -> Give you extra features
            'page_priority' => null, // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
            'page_parent' => 'options-general.php', // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
            'page_permissions' => 'manage_options', // Permissions needed to access the options panel.
            'menu_icon' => '', // Specify a custom URL to an icon
            'last_tab' => '', // Force your panel to always open to a specific tab (by id)
            'page_icon' => 'icon-themes', // Icon displayed in the admin panel next to your menu_title
            'page_slug' => TBB_COMPOSER_SLUG . '_settings', // Page slug used to denote the panel
            'save_defaults' => true, // On load save the defaults to DB before user clicks save or not
            'default_show' => false, // If true, shows the default value next to each field that is not the default value.
            'default_mark' => '', // What to print by the field's title if the value shown is default. Suggested: *
            'show_import_export' => false // Shows the Import/Export panel when not used as a field.
        );
    }
    
    public function getSections()
    {
        $sections   = array();
        $sections[] = $this->getGeneralSection();
        $sections[] = $this->getComposerSection();
        
        return $sections;
    }
    
    private function getGeneralSection()
    {
        return array(
            'icon' => 'el-icon-home-alt',
            'title' => __('General', TBB_COMPOSER_SLUG),
            'fields' => array(
                array(
                    'id' => 'composer_phar_path',
                    'type' => 'text',
                    'title' => __('Path of composer.phar', TBB_BRANDING_SLUG),
                    'subtitle' => __('Change the path of your composer.phar library. You can use the ABSPATH constant like %%ABSPATH%%/composer.phar', TBB_BRANDING_SLUG),
                    'default' => ''
                ),
                array(
                    'id' => 'composer_home_path',
                    'type' => 'text',
                    'title' => __('Path of $COMPOSER_HOME', TBB_BRANDING_SLUG),
                    'subtitle' => __('Change the path of your $COMPOSER_HOME variable. You can use the ABSPATH constant like %%ABSPATH%%/.composer', TBB_BRANDING_SLUG),
                    'default' => '%%ABSPATH%%/.composer'
                ),
                array(
                    'id' => 'composer_json_path',
                    'type' => 'text',
                    'title' => __('Path of composer.json', TBB_BRANDING_SLUG),
                    'subtitle' => __('Change the path of your composer.json file. You can use the ABSPATH constant like %%ABSPATH%%/composer.json', TBB_BRANDING_SLUG),
                    'default' => ''
                ),
                array(
                    'id' => 'composer_json_init',
                    'type' => 'switch',
                    'title' => __('Init composer.json', TBB_COMPOSER_SLUG),
                    'subtitle' => __('Create a default composer.json, if none exists. Path above needs to be set.', TBB_COMPOSER_SLUG),
                    'default' => true
                )
            )
        );
    }
    
    private function getComposerSection()
    {
        return array(
            'icon' => 'el-icon-cogs',
            'title' => __('Composer', TBB_COMPOSER_SLUG),
            'fields' => array(
                array(
                    'id' => 'full_text_search',
                    'type' => 'switch',
                    'title' => __('Full Text Search', TBB_COMPOSER_SLUG),
                    'subtitle' => __('Enable full text package search.', TBB_COMPOSER_SLUG),
                    'default' => true
                ),
                array(
                    'id' => 'verbose',
                    'type' => 'switch',
                    'title' => __('Verbosity', TBB_COMPOSER_SLUG),
                    'subtitle' => __('Enable verbose mode for composer messaging.', TBB_COMPOSER_SLUG),
                    'default' => false
                ),
                array(
                    'id' => 'dry_run',
                    'type' => 'switch',
                    'title' => __('Dry Run', TBB_COMPOSER_SLUG),
                    'subtitle' => __('Only test the Composer update process.', TBB_COMPOSER_SLUG),
                    'default' => false
                ),
                array(
                    'id' => 'optimize_autoloader',
                    'type' => 'switch',
                    'title' => __('Optimize Autoloader', TBB_COMPOSER_SLUG),
                    'subtitle' => __('Whether to optimize the autoloader by Composer.', TBB_COMPOSER_SLUG),
                    'default' => true
                ),
                array(
                    'id' => 'dev_mode',
                    'type' => 'switch',
                    'title' => __('Dev Mode', TBB_COMPOSER_SLUG),
                    'subtitle' => __('Whether to install require-dev packages.', TBB_COMPOSER_SLUG),
                    'default' => false
                ),
                array(
                    'id' => 'debug',
                    'type' => 'switch',
                    'title' => __('Debug', TBB_COMPOSER_SLUG),
                    'subtitle' => __('Enable debug mode for composer usage.', TBB_COMPOSER_SLUG),
                    'default' => false
                )
                
                
            )
        );
    }
    
    public function addMenu()
    {
        add_menu_page(__('Packages', TBB_COMPOSER_SLUG), __('Packages', TBB_COMPOSER_SLUG), 'manage_options', TBB_COMPOSER_SLUG, array(
            $this,
            'renderInstalledView'
        ), plugins_url('assets/img/package_green.png', __FILE__));
        add_submenu_page(TBB_COMPOSER_SLUG, __('Installed Packages', TBB_COMPOSER_SLUG), __('Installed Packages', TBB_COMPOSER_SLUG), 'manage_options', TBB_COMPOSER_SLUG, array(
            $this,
            'renderInstalledView'
        ));
        add_submenu_page(TBB_COMPOSER_SLUG, __('Search Packages', TBB_COMPOSER_SLUG), __('Search Packages', TBB_COMPOSER_SLUG), 'manage_options', TBB_COMPOSER_SLUG . '_search', array(
            $this,
            'renderSearchView'
        ));
        
        $this->enqueueHook = add_submenu_page(TBB_COMPOSER_SLUG, __('Update Project', TBB_COMPOSER_SLUG), __('Update Project', TBB_COMPOSER_SLUG), 'manage_options', TBB_COMPOSER_SLUG . '_update', array(
            $this,
            'renderUpdateView'
        ));
        
        add_action('admin_enqueue_scripts', array(
            $this,
            'enqueueScripts'
        ));
    }
    
    public function getErrorMessage($message, $warning = true)
    {
        $output = '<div class="error"><p>';
        $output .= $message;
        
        if ($warning) {
            $output .= '<br>';
            $output .= '<br>';
            $output .= sprintf(__('Please make sure, you configured your composer.json path in the <a href="%s">Settings</a> properly!', TBB_COMPOSER_SLUG), get_admin_url() . 'options-general.php?page=' . TBB_COMPOSER_SLUG . '_settings');
        }
        
        $output .= '</p></div>';
        
        return $output;
        
    }
    
    public function addPharErrorMessage()
    {
        $output = '<div class="error"><p>';
        
        $output .= sprintf(__('Please make sure, you configured your composer.phar path in the <a href="%s">Settings</a> properly!', TBB_COMPOSER_SLUG), get_admin_url() . 'options-general.php?page=' . TBB_COMPOSER_SLUG . '_settings');
        
        $output .= '</p></div>';
        
        echo $output;
        
    }
    
    public function getStatusMessage($status)
    {
        $output = '<div class="updated"><ul>';
        
        foreach ($status as $message) {
            $output .= '<li>';
            $output .= $message;
            $output .= '</li>';
        }
        
        $output .= '</ul></div>';
        
        return $output;
        
    }
    
    public function renderInstalledView()
    {
        try {
            $composer = Factory::create(new NullIO(), $this->settings['composer_json_path']);
            
            $lockedRepo = $composer->getLocker()->getLockedRepository(true);
            
            $packages = array();
            
            foreach ($lockedRepo->getPackages() as $package) {
                $packages[] = array(
                    'name' => $package->getName(),
                    'description' => $package->getDescription(),
                    'version' => $package->getPrettyVersion()
                );
            }
            
            $table = new ArrayPackagesTable($packages);
            $table->prepare_items();
            
        }
        catch (Exception $e) {
            $messages = $this->getErrorMessage($e->getMessage());
        }
        
        
        include_once(dirname(__FILE__) . '/view/installed.php');
    }
    
    public function renderSearchView()
    {
        
        if (isset($_GET['s'])) {
            try {
                $composer = Factory::create(new NullIO(), $this->settings['composer_json_path']);
                
                $platformRepo  = new PlatformRepository;
                $localRepo     = $composer->getRepositoryManager()->getLocalRepository();
                $installedRepo = new CompositeRepository(array(
                    $localRepo,
                    $platformRepo
                ));
                $repos         = new CompositeRepository(array_merge(array(
                    $installedRepo
                ), $composer->getRepositoryManager()->getRepositories()));
                
                $flags = $this->settings['full_text_search'] ? RepositoryInterface::SEARCH_FULLTEXT : RepositoryInterface::SEARCH_NAME;
                
                $results = $repos->search($_GET['s'], $flags);
                
                $table = new ArrayPackagesTable($results);
                $table->prepare_items();
            }
            catch (Exception $e) {
                $messages = $this->getErrorMessage($e->getMessage());
                include_once(dirname(__FILE__) . '/view/results.php');
                return;
            }
            
            include_once(dirname(__FILE__) . '/view/results.php');
        } else {
            include_once(dirname(__FILE__) . '/view/search.php');
        }
        
    }
    
    public function renderUpdateView()
    {
        if (isset($_POST['composer'])) {
            $this->renderUpdateViewPost();
        } else {
            $this->renderUpdateViewNormal();
        }
        
    }
    
    public function renderUpdateViewPost()
    {
        $path        = $this->settings['composer_json_path'];
        $file_exists = file_exists($path);
        
        
        try {
            $new_json = stripslashes($_POST['composer']);
            $file     = new JsonFile($path);
            $json     = $new_json;
            $file->write(JsonFile::parseJson($new_json));
            
            require_once dirname(__FILE__) . '/class.tbb-composer-io.php';
            $io = new TBBComposerIO($this->settings['verbose'], $this->settings['debug']);
            
            $composer = Factory::create($io, $path);
            $install  = Installer::create($io, $composer);
            
            $install->setDryRun($this->settings['dry_run'])->setVerbose($this->settings['verbose'])->setOptimizeAutoloader($this->settings['optimize_autoloader'])->setDevMode($this->settings['dev_mode'])->setUpdate(true);
            
            $install->run();
            
            $messages = $this->getStatusMessage($io->status);
            
        }
        catch (Exception $e) {
            $messages = $this->getErrorMessage($e->getMessage(), false);
        }
        
        include_once(dirname(__FILE__) . '/view/update.php');
    }
    
    public function renderUpdateViewNormal()
    {
        $path        = $this->settings['composer_json_path'];
        $file_exists = file_exists($path);
        
        
        try {
            
            $file = new JsonFile($path);
            
            if (!empty($path) && !$file_exists && $this->settings['composer_json_init']) {
                
                $options = array(
                    'name' => get_bloginfo('name'),
                    'description' => get_bloginfo('description'),
                    'homepage' => get_bloginfo('url'),
                    'require' => array(),
                    'require-dev' => array()
                );
                
                
                $file->write($options);
            }
            
            $json = JsonFile::encode($file->read());
            
            
        }
        catch (Exception $e) {
            $messages = $this->getErrorMessage($e->getMessage());
        }
        
        include_once(dirname(__FILE__) . '/view/update.php');
    }
    
    public function enqueueScripts($hook)
    {
        
        if ($hook == $this->enqueueHook) {
            wp_enqueue_script('ace_editor', plugins_url('assets/ace/ace.js', __FILE__));
            wp_enqueue_script('jquery');
        }
    }
    
    public function check()
    {
        /**
         * Array of plugin arrays. Required keys are name and slug.
         * If the source is NOT from the .org repo, then source is also required.
         */
        $plugins = array(
            
            // This is an example of how to include a plugin from the WordPress Plugin Repository.
            array(
                'name' => 'Redux Framework',
                'slug' => 'redux-framework',
                'required' => true
            )
            
        );
        
        $config = array( // Message to output right before the plugins table.
            'strings' => array(
                'notice_can_install_required' => __("The Blackest Box's Composer requires the following plugin: %1$s.", TBB_COMPOSER_SLUG)
            )
        );
        
        tgmpa($plugins, $config);
    }
    
    
}