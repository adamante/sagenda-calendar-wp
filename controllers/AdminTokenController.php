<?php namespace SagendaCalendar\Controllers;
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
use SagendaCalendar\webservices\sagendaAPI;
include_once( SAGENDA_CALENDAR_PLUGIN_DIR . 'webservices/SagendaAPI.php' );

/**
* This controller will be responsible for displaying the Settings in WP backend.
*/
class AdminTokenController
{
  /**
  * @var string - name of the view to be displayed
  */
  private $view = "adminToken.twig" ;

  /**
  * Display the search events form
  * @param  object  $twig   TWIG template renderer
  */
  public function showAdminTokenSettingsPage($twig)
  {
    $tokenValue = $this->getAuthenticationToken();
    $this->saveAuthenticationToken();

    $sagendaAPI = new sagendaAPI();
    $result = $sagendaAPI->validateAccount($tokenValue);
    $color = "red";
    $connectedStatus = __( 'NOT CONNECTED', 'sagenda-calendar-wp' );
 
    if($result['didSucceed'] && $tokenValue != null )
    {
      $color = "green";
      $connectedStatus  = __( 'CONNECTED', 'sagenda-calendar-wp' );
    }

    return $twig->render($this->view, array(
      'SAGENDA_CALENDAR_PLUGIN_URL'                    => SAGENDA_CALENDAR_PLUGIN_URL,
      'sagendaAuthenticationSettings'         => __( 'Sagenda Authentication Settings', 'sagenda-calendar-wp' ),
      'sagendaAuthenticationCode'             => __( 'Sagenda Authentication Code', 'sagenda-calendar-wp' ),
      'saveChanges'                           => __( 'Save Changes', 'sagenda-calendar-wp' ),
      'currentStatus'                         => __( 'Current Status', 'sagenda-calendar-wp' ),
      'clickHereToGetYourAuthenticationCode'  => __( 'Click here to get your Authentication code.', 'sagenda-calendar-wp' ),
      'shortCodeInfo'                         => __( '<strong>[sagenda-calendar-wp]</strong> add this shortcode either in a post or page where you want to display the Sagenda form.', 'sagenda-calendar-wp' ),
      'shortCodeOptionDefaultView'            => __( '<strong>[sagenda-calendar-wp defaultView="x"]</strong> to define the view you want to open by default, where x is "monthy", "week", "day".', 'sagenda-calendar-wp' ),
      'shortCodeOptionFixedBookableItem'      => __( '<strong>[sagenda-calendar-wp fixedbookableitem="x"]</strong> where x is the "id" of your bookable item. To know the identifier of your bookable item, you can just go to your bookable item list :  <a href="https://sagenda.net/BookableItems/List" target="_blank">https://sagenda.net/BookableItems/List</a> and select "edit". You will find a button to copy the identifier.', 'sagenda-calendar-wp' ),
      'shortCodeInfoInPHP'                    => __( 'If you want to use a shortcode outside of the WordPress post or page editor, you can use this snippet to output it from the shortcode’s handler(s): <pre>echo do_shortcode([sagenda-calendar-wp])</pre>', 'sagenda-calendar-wp' ),
      'registeringInfo'                       => __( 'NOTE: You first need to register on Sagenda and then you will get a Authentication token which you will use to validate this Sagenda Plugin.', 'sagenda-calendar-wp' ),
      'readMore'                              => __( 'Read more', 'sagenda-calendar-wp' ),
      'createAccount'                         => __( 'Create a free account ', 'sagenda-calendar-wp' ),
      'aboutIntegrationTitle'                 => __( 'About integration in your WP WebSite :', 'sagenda-calendar-wp' ),
      'howToGetTheTokenTitle'                 => __( 'How to get the token :', 'sagenda-calendar-wp' ),
      'usefulLinksTitle'                      => __( 'Useful links :', 'sagenda-calendar-wp' ),
      'result'                                => $result,
      'color'                                 => $color,
      'connectedStatus'                       => $connectedStatus,
      'tokenValue'                            => $tokenValue,
    ));
  }

  /**
  * Get the authentication code from db or formulary
  * @return string  user authentication code
  */
  private function getAuthenticationToken()
  {
    if(isset($_POST['sagendaAuthenticationCode']))
    {
      return $_POST['sagendaAuthenticationCode'];
    }
    else
    {
      return get_option('mrs1_authentication_code');
    }
  }

  /**
  * Save the authentication code from formulary to db
  */
  private function saveAuthenticationToken()
  {
    if(isset($_POST['sagendaAuthenticationCode']))
    {
      // add option does nothing if already exist. So try to create, if exist update the value.
      add_option( 'mrs1_authentication_code', $_POST['sagendaAuthenticationCode'], '', 'yes' );
      update_option('mrs1_authentication_code', $_POST['sagendaAuthenticationCode']);
    }
  }
}
