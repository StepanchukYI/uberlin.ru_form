<?php
/**
 * Created by PhpStorm.
 * User: b0dun
 * Date: 10.12.2017
 * Time: 11:43
 */

if ( ! class_exists( 'WP_List_Table' ) )
{
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class TT_Example_List_Table extends WP_List_Table
{


	/** ************************************************************************
	 * REQUIRED. Set up a constructor that references the parent constructor. We
	 * use the parent reference to set some default configs.
	 ***************************************************************************/
	function __construct()
	{
		global $status, $page;

		//Set parent defaults
		parent::__construct( array(
			'singular' => 'Пользователь',     //singular name of the listed records
			'plural'   => 'пользователи',    //plural name of the listed records
			'ajax'     => false        //does this table support ajax?
		) );

	}


	/** ************************************************************************
	 * Recommended. This method is called when the parent class can't find a method
	 * specifically build for a given column. Generally, it's recommended to include
	 * one method for each column you want to render, keeping your package class
	 * neat and organized. For example, if the class needs to process a column
	 * named 'title', it would first see if a method named $this->column_title()
	 * exists - if it does, that method will be used. If it doesn't, this one will
	 * be used. Generally, you should try to use custom column methods as much as
	 * possible.
	 *
	 * Since we have defined a column_title() method later on, this method doesn't
	 * need to concern itself with any column with a name of 'title'. Instead, it
	 * needs to handle everything else.
	 *
	 * For more detailed insight into how columns are handled, take a look at
	 * WP_List_Table::single_row_columns()
	 *
	 * @param array $item        A singular item (one full row's worth of data)
	 * @param array $column_name The name/slug of the column to be processed
	 *
	 * @return string Text or HTML to be placed inside the column <td>
	 **************************************************************************/
	function column_default( $item, $column_name )
	{
		switch ( $column_name )
		{
			case 'fiouser':
			case 'email':
			case 'phonenumber':
			case 'city':
			case 'passport':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}


	/** ************************************************************************
	 * Recommended. This is a custom column method and is responsible for what
	 * is rendered in any column with a name/slug of 'title'. Every time the class
	 * needs to render a column, it first looks for a method named
	 * column_{$column_title} - if it exists, that method is run. If it doesn't
	 * exist, column_default() is called instead.
	 *
	 * This example also illustrates how to implement rollover actions. Actions
	 * should be an associative array formatted as 'slug'=>'link html' - and you
	 * will need to generate the URLs yourself. You could even ensure the links
	 *
	 *
	 * @see WP_List_Table::::single_row_columns()
	 *
	 * @param array $item A singular item (one full row's worth of data)
	 *
	 * @return string Text to be placed inside the column <td> (movie title only)
	 **************************************************************************/
	function column_fiouser( $item )
	{
		//Build row actions
		$actions = array(
			'edit'   => sprintf( '<a href="?page=%s&action=%s&user=%s">Редактировать</a>', 'custom_reg_form', 'edit', $item['id'] ),
			'delete' => sprintf( '<a href="?page=%s&action=%s&user=%s">Удалить</a>', 'custom_reg_form', 'delete', $item['id'] ),
		);

		//Return the title contents
		return sprintf( '%1$s <span style="color:silver">(Id:%2$s)</span>%3$s',
			/*$1%s*/
			$item['fiouser'],
			/*$2%s*/
			$item['id'],
			/*$3%s*/
			$this->row_actions( $actions )
		);
	}


	/** ************************************************************************
	 * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
	 * is given special treatment when columns are processed. It ALWAYS needs to
	 * have it's own method.
	 *
	 * @see WP_List_Table::::single_row_columns()
	 *
	 * @param array $item A singular item (one full row's worth of data)
	 *
	 * @return string Text to be placed inside the column <td> (movie title only)
	 **************************************************************************/
	function column_cb( $item )
	{
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/
			$this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
			/*$2%s*/
			$item['id']                //The value of the checkbox should be the record's id
		);

	}


	/** ************************************************************************
	 * REQUIRED! This method dictates the table's columns and titles. This should
	 * return an array where the key is the column slug (and class) and the value
	 * is the column's title text. If you need a checkbox for bulk actions, refer
	 * to the $columns array below.
	 *
	 * The 'cb' column is treated differently than the rest. If including a checkbox
	 * column in your table you must create a column_cb() method. If you don't need
	 * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
	 *
	 * @see WP_List_Table::::single_row_columns()
	 * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
	 **************************************************************************/
	function get_columns()
	{
		$columns = array(
			'cb'          => '<input type="checkbox" />', //Render a checkbox instead of text
			'fiouser'     => 'ФИО',
			'email'       => 'Email',
			'phonenumber' => 'Номер телефона',
			'city'        => 'Город',
			'passport'    => 'Пасспорт',
		);

		return $columns;
	}


	/** ************************************************************************
	 * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
	 * you will need to register it here. This should return an array where the
	 * key is the column that needs to be sortable, and the value is db column to
	 * sort by. Often, the key and value will be the same, but this is not always
	 * the case (as the value is a column name from the database, not the list table).
	 *
	 * This method merely defines which columns should be sortable and makes them
	 * clickable - it does not handle the actual sorting. You still need to detect
	 * the ORDERBY and ORDER querystring variables within prepare_items() and sort
	 * your data accordingly (usually by modifying your query).
	 *
	 * @return array An associative array containing all the columns that should be sortable:
	 *               'slugs'=>array('data_values',bool)
	 **************************************************************************/
	function get_sortable_columns()
	{
		$sortable_columns = array(
			'fiouser'     => array( 'fiouser', false ),
			'email'       => array( 'email', false ),
			'phonenumber' => array( 'phonenumber', false ),
			'city'        => array( 'city', false ),
			'passport'    => array( 'passport', false ),
		);

		return $sortable_columns;
	}


	/** ************************************************************************
	 * Optional. If you need to include bulk actions in your list table, this is
	 * the place to define them. Bulk actions are an associative array in the format
	 * 'slug'=>'Visible Title'
	 *
	 * If this method returns an empty value, no bulk action will be rendered. If
	 * you specify any bulk actions, the bulk actions box will be rendered with
	 * the table automatically on display().
	 *
	 * Also note that list tables are not automatically wrapped in <form> elements,
	 * so you will need to create those manually in order for bulk actions to function.
	 *
	 * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
	 **************************************************************************/
	function get_bulk_actions()
	{
		$actions = array(
			'delete' => 'Delete',
		);

		return $actions;
	}


	/** ************************************************************************
	 * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
	 * For this example package, we will handle it in the class to keep things
	 * clean and organized.
	 *
	 * @see $this->prepare_items()
	 **************************************************************************/
	function process_bulk_action()
	{

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() )
		{
			wp_die( 'Items deleted (or they would be if we had items to delete)!' );
		}

	}


	/** ************************************************************************
	 * REQUIRED! This is where you prepare your data for display. This method will
	 * usually be used to query the database, sort and filter the data, and generally
	 * get it ready to be displayed. At a minimum, we should set $this->items and
	 * $this->set_pagination_args(), although the following properties and methods
	 * are frequently interacted with here...
	 *
	 * @global WPDB $wpdb
	 * @uses $this->_column_headers
	 * @uses $this->items
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->set_pagination_args()
	 **************************************************************************/
	function prepare_items()
	{
		global $wpdb; //This is used only if making any database queries

		/**
		 * First, lets decide how many records per page to show
		 */
		$per_page = 10;


		/**
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();


		/**
		 * REQUIRED. Finally, we build an array to be used by the class for column
		 * headers. The $this->_column_headers property takes an array which contains
		 * 3 other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		 */
		$this->_column_headers = array( $columns, $hidden, $sortable );


		/**
		 * Optional. You can handle your bulk actions however you see fit. In this
		 * case, we'll handle them within our package just to keep things clean.
		 */
		$this->process_bulk_action();


		/**
		 * Instead of querying a database, we're going to fetch the example data
		 * property we created for use in this plugin. This makes this example
		 * package slightly different than one you might build on your own. In
		 * this example, we'll be using array manipulation to sort and paginate
		 * our data. In a real-world implementation, you will probably want to
		 * use sort and pagination data to build a custom query instead, as you'll
		 * be able to use your precisely-queried data immediately.
		 */
		$data = get_user_data();


		/**
		 * This checks for sorting input and sorts the data in our array accordingly.
		 *
		 * In a real-world situation involving a database, you would probably want
		 * to handle sorting by passing the 'orderby' and 'order' values directly
		 * to a custom query. The returned data will be pre-sorted, and this array
		 * sorting technique would be unnecessary.
		 */
		function usort_reorder( $a, $b )
		{
			$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'fiouser'; //If no sort, default to title
			$order   = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
			$result  = strcmp( $a[ $orderby ], $b[ $orderby ] ); //Determine sort order

			return ( $order === 'asc' ) ? $result : - $result; //Send final sort direction to usort
		}

		usort( $data, 'usort_reorder' );


		/***********************************************************************
		 * ---------------------------------------------------------------------
		 * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
		 *
		 * In a real-world situation, this is where you would place your query.
		 *
		 * For information on making queries in WordPress, see this Codex entry:
		 * http://codex.wordpress.org/Class_Reference/wpdb
		 *
		 * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
		 * ---------------------------------------------------------------------
		 **********************************************************************/


		/**
		 * REQUIRED for pagination. Let's figure out what page the user is currently
		 * looking at. We'll need this later, so you should always include it in
		 * your own package classes.
		 */
		$current_page = $this->get_pagenum();

		/**
		 * REQUIRED for pagination. Let's check how many items are in our data array.
		 * In real-world use, this would be the total number of items in your database,
		 * without filtering. We'll need this later, so you should always include it
		 * in your own package classes.
		 */
		$total_items = count( $data );


		/**
		 * The WP_List_Table class does not handle pagination for us, so we need
		 * to ensure that the data is trimmed to only the current page. We can use
		 * array_slice() to
		 */
		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );


		/**
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		 */
		$this->items = $data;


		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
			'total_pages' => ceil( $total_items / $per_page )   //WE have to calculate the total number of pages
		) );
	}


}

register_activation_hook( __FILE__, 'testplugin_install' );
function testplugin_install()
{
	global $wpdb;
	$table_name = $wpdb->prefix . "custom_registration_form_uberlin";
	// создание таблицы данных плагина в базе данных
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name )
	{
		$sql = "CREATE TABLE " . $table_name . " (
  id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
  fiouser TEXT NULL,
  bdate   TEXT NULL,
  bplace     TEXT NULL,
  phonenumber   TEXT NULL,
  email      TEXT NULL,
  city       TEXT NULL,  
  passport    TEXT NULL,
  rovInfo     TEXT NULL,
  passDate    TEXT NULL,
  address     TEXT NULL,
  bik         TEXT NULL,
  korrBank    TEXT NULL,
  bankName    TEXT NULL,
  poluchCode  TEXT NULL,
  bankCard    TEXT NULL,
  helpInfo    TEXT NULL,
  comment  TEXT NULL,
  UNIQUE KEY id (id);
 );";


		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );


	}
}

/** Step 2 (from text above). */
add_action( 'admin_menu', 'my_plugin_menu' );

/** Step 1. */
function my_plugin_menu()
{
	add_menu_page( 'Таблица пользователей', 'Custom Registration Form', 'manage_options', 'custom_reg_form', 'my_plugin_options' );
}

/** Step 3. */
function my_plugin_options()
{

	if ( $_GET['action'] == 'edit' )
	{
		$user = get_single_user_data( $_GET['user'] )[0];

		$local = [
			'fiouser'       => 'ФИО',
			'bdate'         => 'Дата рождения',
			'bplace'        => 'Место рождения',
			'email'         => 'Укажите вашу действующую почту',
			'phonenumber'   => 'Номер телефона',
			'city'          => 'Город',
			'helpInfo'      => 'Откуда узнали',
			'passport'      => 'Серия и номер паспорта',
			'rovInfo'       => 'Код подразделения, который выдал паспорт',
			'passDate'      => 'Дата выдачи паспорта',
			'address'       => 'Адрес по прописке',
			'passFrontPage' => 'Загрузить разворот паспорта',
			'FIOPoluch'     => 'ФИО получателя средств',
			'bik'           => 'БИК банка',
			'korrBank'      => 'Корр. счет банка',
			'bankName'      => 'Наименование банка',
			'poluchCode'    => 'Счет получателя',
			'bankCard'      => 'Номер карты',
			'comment'       => 'Коментарий',
			'personalData'  => 'Соглашение на обработку персональных данных ',
		];


		$data = '
        <div class="wrap">

            <div id="icon-users" class="icon32"><br/></div>
            <h2>Пользователь</h2>

            <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
            <form id="movies-filter" method="POST">
            
                <input id="id" type="hidden"  value="' . $user['id'] . '"/> 
                <div>                
                    <label class="" for="fiouser">' . $local['fiouser'] . '</label>
                    <input id="fiouser" type="text" required name="fiouser" placeholder="Укажите ФИО полностью" value="' . $user['fiouser'] . '"/>                    
                </div>

                <div>
                    <label class="" for="bdate">' . $local['bdate'] . '</label>
                    <input id="bdate" type="text" required name="bdate" placeholder="ДД.ММ.ГГГГ" value="' . $user['bdate'] . '"/>
                </div>

                <div>
                    <label class="" for="bplace">' . $local['bplace'] . '</label>
                    <input id="bplace" type="text" name="bplace" placeholder="Как в паспорте" value="' . $user['bplace'] . '"/>
                </div>

                <div>
                    <label class="" for="phonenumber">' . $local['phonenumber'] . '</label>
                    <input id="phonenumber" type="text" required name="phone" placeholder="7(999) 999-9999" value="' . $user['phonenumber'] . '" />
                </div>

                <div>
                    <label class="" for="email">' . $local['email'] . '</label>
                    <input id="email" type="text" name="email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" placeholder="' . $local['email'] . '" value="' . $user['email'] . '"/>
                </div>

                <div>
                    <label class="" for="city">' . $local['city'] . '</label>
                    <select class="select" name="city" id="city">
                        <option value="' . $user['city'] . '">' . $user['city'] . '</option>
                        <option value="">' . $local['city'] . '</option>
                        <option value="Воронеж">Воронеж</option>
                        <option value="Екатеринбург">Екатеринбург</option>
                        <option value="Казань">Казань</option>
                        <option value="Краснодар">Краснодар</option>
                        <option value="Красноярск">Красноярск</option>
                        <option value="Москва">Москва</option>
                        <option value="Нижний Новгород">Нижний Новгород</option>
                        <option value="Новосибирск">Новосибирск</option>
                        <option value="Омск">Омск</option>
                        <option value="Пермь">Пермь</option>
                        <option value="Ростов">Ростов</option>
                        <option value="Самара">Самара</option>
                        <option value="Санкт Петербург">Санкт Петербург</option>
                        <option value="Сочи">Сочи</option>
                        <option value="Уфа">Уфа</option>
                        <option value="Челябинск">Челябинск</option>
                        <option value="Тольятти">Тольятти</option>
                    </select>
                </div>

                <div>
                    <label class="" for="helpInfo">' . $local['helpInfo'] . '</label>
                    <select class="select" name="helpInfo" id="helpInfo">
                        <option value="' . $user['helpInfo'] . '">' . $user['helpInfo'] . '</option>
                        <option value="">' . $local['helpInfo'] . '</option>
                        <option value="Авито">Авито</option>
                        <option value="Яндекс">Яндекс</option>
                        <option value="Гугл">Гугл</option>
                        <option value="Сайт вакансий">Сайт вакансий</option>
                        <option value="Рекомендация друзей/знакомых">Рекомендация друзей/знакомых</option>
                    </select>
                </div>

                <div>
                    <label class="" for="passport">' . $local['passport'] . '</label>
                    <input id="passport" type="text" name="passport" placeholder="12 34 567890" value="' . $user['passport'] . '"/>
                </div>

                <div>
                    <label class="" for="rovInfo">' . $local['rovInfo'] . '</label>
                    <input id="rovInfo" type="text" name="rovInfo" placeholder="123-456" value="' . $user['rovInfo'] . '"/>
                </div>

                <div>
                    <label class="" for="passDate">' . $local['passDate'] . '</label>
                    <input id="passDate" type="text" name="passDate" placeholder="ДД.ММ.ГГГГ" value="' . $user['passDate'] . '"/>
                </div>

                <div>
                    <label class="" for="address">' . $local['address'] . '</label>
                    <input id="address" type="text" name="address" placeholder="Полный адрес прописки, как в паспорте" value="' . $user['address'] . '"/>
                </div>

                <div>
                    <label class="" for="FIOPoluch">' . $local['FIOPoluch'] . '</label>
                    <input id="FIOPoluch" type="text" name="FIOPoluch" placeholder="ФИО получателя полностью" value="' . $user['FIOPoluch'] . '"/>
                </div>

                <div>
                    <label class="" for="bankName">' . $local['bankName'] . '</label>
                    <input id="bankName" type="text" name="bankName" placeholder="' . $local['bankName'] . '" value="' . $user['bankName'] . '" maxlength="18"/>
                </div>

                <div>
                    <label class="" for="bankName">Номер счета</label>
                    <input id="poluchCode" type="text" name="poluchCode" maxlength="20" placeholder="Начинается с 40817810" value="' . $user['poluchCode'] . '"/>
                </div>

                <div>
                    <label class="" for="bik">' . $local['bik'] . '</label>
                    <input id="bik" type="text" name="bik" maxlength="9" placeholder="Начинаются с 04" value="' . $user['bik'] . '"/>
                </div>
                
                <div>
                    <label class="" for="korrBank">' . $local['korrBank'] . '</label>
                    <input id="korrBank" type="text" name="korrBank" maxlength="20" placeholder="Начинается с 301" value="' . $user['korrBank'] . '"/>
                </div>

                <div>
                    <label for="bankCard">' . $local['bankCard'] . '<strong style="color: red">*</strong></label>
                    <input id="bankCard" type="text" name="bankCard" maxlength="18" placeholder="' . $local['bankCard'] . '" value="' . $user['bankCard'] . '"/>
                </div>
                
                <div>
                    <label for="comment">' . $local['comment'] . '</label>
                    <textarea id="comment" rows="4" name="comment" placeholder="' . $local['comment'] . '">' . $user['comment'] . '</textarea>
                </div>
                
                <input class="acf-button button button-primary button-large" type="button" name="buttonSubmit" id="buttonSubmit" value="Отправить"/>
            </form>
        </div>
		';
		$data .= "<script>

        // var new_reg_ajax_url = 'http://uberlin.ru/wp-admin/admin-ajax.php';
        var new_reg_ajax_url = 'http://37.57.92.40/wp-3/wp-admin/admin-ajax.php';
        
        jQuery(document).ready(function () {
            jQuery('#buttonSubmit').click(function () {               
                var form_data = new FormData();
                form_data.append('action', 'custom_registration_update_ajax');
                form_data.append('fiouser', jQuery('#fiouser').val());
                form_data.append('email', jQuery('#email').val());
                form_data.append('phonenumber', jQuery('#phonenumber').val());
                form_data.append('city', jQuery('#city').val());
                form_data.append('bankCard', jQuery('#bankCard').val());
                form_data.append('bdate', jQuery('#bdate').val());
                form_data.append('bplace', jQuery('#bplace').val());
                form_data.append('helpInfo', jQuery('#helpInfo').val());
                form_data.append('passport', jQuery('#passport').val());
                form_data.append('rovInfo', jQuery('#rovInfo').val());
                form_data.append('FIOPoluch', jQuery('#FIOPoluch').val());
                form_data.append('passDate', jQuery('#passDate').val());
                form_data.append('address', jQuery('#address').val());
                form_data.append('bik', jQuery('#bik').val());
                form_data.append('korrBank', jQuery('#korrBank').val());
                form_data.append('bankName', jQuery('#bankName').val());
                form_data.append('poluchCode', jQuery('#poluchCode').val());
                form_data.append('comment', jQuery('#comment').val());
                form_data.append('id', jQuery('#id').val());
                jQuery.ajax({
                    type: 'post',
                    url: new_reg_ajax_url,
                    data: form_data,
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    success: function (response) {                        
                        console.log('success');
                        alert('Сохранено!');
                        window.location.href = 'admin.php?page=custom_reg_form';
                        
                    }
                });
                        });
            });
</script>";
		echo $data;

	} elseif ( $_GET['action'] == 'dalete' )
	{
		delete_user_data( $_GET['user'] );
		header( 'admin.php?page=custom_reg_form' );
	} else
	{
		//Create an instance of our package class...
		$testListTable = new TT_Example_List_Table();
		//Fetch, prepare, sort, and filter our data...
		$testListTable->prepare_items();

		?>
        <div class="wrap">

            <div id="icon-users" class="icon32"><br/></div>
            <h2>Пользователи</h2>

            <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
            <form id="movies-filter" method="get">
                <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
                <!-- Now we can render the completed list table -->
				<?php $testListTable->display() ?>
            </form>
            <div>
                <button id="userDataDownload">Выгрузить данные пользователей</button>
            </div>
            <script>
                var new_reg_ajax_url = 'http://uberlin.ru/wp-admin/admin-ajax.php';
                // var new_reg_ajax_url = 'http://37.57.92.40/wp-3/wp-admin/admin-ajax.php';

                jQuery('#userDataDownload').click(function () {
                    jQuery.ajax({
                        type: "post",
                        url: new_reg_ajax_url,
                        data: {action: "user_data_export_ajax"},
                        dataType: 'json',
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            console.log("DONE");
                        }
                    });
                });
            </script>
        </div>
		<?php
	}

}

add_action( 'wp_ajax_custom_registration_update_ajax', 'custom_registration_update_ajax' );
add_action( 'wp_ajax_nopriv_custom_registration_update_ajax', 'custom_registration_update_ajax' );
function custom_registration_update_ajax()
{

	$id          = $_REQUEST['id'];
	$fiouser     = $_REQUEST['fiouser'];
	$bdate       = $_REQUEST['bdate'];
	$bplace      = $_REQUEST['bplace'];
	$phonenumber = $_REQUEST['phonenumber'];
	$email       = $_REQUEST['email'];
	$city        = $_REQUEST['city'];
	$helpInfo    = $_REQUEST['helpInfo'];
	$passport    = $_REQUEST['passport'];
	$rovInfo     = $_REQUEST['rovInfo'];
	$passDate    = $_REQUEST['passDate'];
	$address     = $_REQUEST['address'];
	$FIOPoluch     = $_REQUEST['FIOPoluch'];
	$bik         = $_REQUEST['bik'];
	$korrBank    = $_REQUEST['korrBank'];
	$bankName    = $_REQUEST['bankName'];
	$poluchCode  = $_REQUEST['poluchCode'];
	$bankCard    = $_REQUEST['bankCard'];
	$comment     = $_REQUEST['comment'];

	$response = edit_registration( $fiouser, $bdate, $bplace, $email, $phonenumber,
		$city, $helpInfo, $passport, $passDate, $rovInfo, $address,
		$bik, $korrBank, $bankName, $poluchCode, $bankCard, $comment, $id, $FIOPoluch );


	echo json_encode( $response );
	die();
}

function edit_registration(
	$fiouser, $bdate, $bplace, $email, $phonenumber,
	$city, $helpInfo, $passport, $passDate, $rovInfo, $address,
	$bik, $korrBank, $bankName, $poluchCode, $bankCard, $comment, $id, $FIOPoluch
) {

	global $wpdb;
	$table_name = $wpdb->get_blog_prefix() . 'custom_registration_form_uberlin';

	return $wpdb->update( $table_name, array(
		'fiouser'     => $fiouser,
		'bdate'       => $bdate,
		'bplace'      => $bplace,
		'email'       => $email,
		'phonenumber' => $phonenumber,
		'city'        => $city,
		'helpInfo'    => $helpInfo,
		'passport'    => $passport,
		'passDate'    => $passDate,
		'rovInfo'     => $rovInfo,
		'address'     => $address,
		'$FIOPoluch'  => $FIOPoluch,
		'bik'         => $bik,
		'korrBank'    => $korrBank,
		'bankName'    => $bankName,
		'poluchCode'  => $poluchCode,
		'bankCard'    => $bankCard,
		'comment'     => $comment
	), array( 'id' => $id ) );
}

function get_single_user_data( $id )
{
	global $wpdb;
	$table_name = $wpdb->get_blog_prefix() . 'custom_registration_form_uberlin';
	$users      = $wpdb->get_results( "SELECT * FROM {$table_name} WHERE id = $id", 'ARRAY_A' );

	return $users;
}

function delete_user_data( $id )
{
	global $wpdb;
	$table_name = $wpdb->get_blog_prefix() . 'custom_registration_form_uberlin';
	$users      = $wpdb->delete( $table_name, $id );

	return $users;
}

function get_user_data()
{
	global $wpdb;
	$table_name = $wpdb->get_blog_prefix() . 'custom_registration_form_uberlin';
	$users      = $wpdb->get_results( "SELECT id,fiouser,email,phonenumber,city,passport FROM {$table_name} ORDER BY id DESC ", 'ARRAY_A' );

	return $users;
}

function get_all_user_data()
{
	global $wpdb;
	$table_name = $wpdb->get_blog_prefix() . 'custom_registration_form_uberlin';
	$users      = $wpdb->get_results( "SELECT * FROM {$table_name}", 'ARRAY_A' );

	return $users;
}

add_action( 'wp_ajax_user_data_export_ajax', 'user_data_export_ajax' );
add_action( 'wp_ajax_nopriv_user_data_export_ajax', 'user_data_export_ajax' );
function user_data_export()
{

	function cleanData( &$str )
	{
		$str = preg_replace( "/\t/", "\\t", $str );
		$str = preg_replace( "/\r?\n/", "\\n", $str );
		if ( strstr( $str, '"' ) )
		{
			$str = '"' . str_replace( '"', '""', $str ) . '"';
		}
	}

	$upload_dir = wp_upload_dir();
	// filename for download
	$filename = "website_data_" . date( 'Ymd' ) . ".xls";

	$path = $upload_dir['basedir'] . '/user_data/' . $filename;

	header( "Content-Disposition: attachment; filename=\"$filename\"" );
	header( "Content-Type: application/vnd.ms-excel" );

	$flag   = false;
	$result = get_all_user_data();
	while ( false !== ( $row = pg_fetch_assoc( $result ) ) )
	{
		if ( ! $flag )
		{
			// display field/column names as first row
			echo implode( "\t", array_keys( $row ) ) . "\r\n";
			$flag = true;
		}
		array_walk( $row, __NAMESPACE__ . '\cleanData' );
		echo implode( "\t", array_values( $row ) ) . "\r\n";
	}
	exit;
}