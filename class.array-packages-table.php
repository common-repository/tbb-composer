<?php

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class ArrayPackagesTable extends WP_List_Table
{
    
    private $packages;
    
    function __construct($packages)
    {
        parent::__construct(array(
            'singular' => 'package', //Singular label
            'plural' => 'packages', //plural label, also this well be one of the table css class
            'ajax' => false //We won't support Ajax for this table
        ));
        
        $this->packages = $packages;
    }
    
    
    
    function get_columns()
    {
        return $columns = array(
            'name' => __('Name'),
            'description' => __('Description'),
            'version' => __('Version')
        );
    }
    
    
    public function get_sortable_columns()
    {
        return $sortable = array(
        //'name'=> array('name',false), 
            );
    }
    
    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'name':
            case 'description':
			case 'version':
                return $item[$column_name];
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }
    
    
    function prepare_items()
    {
        
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
         * First, lets decide how many records per page to show
         */
        $per_page = 30;
        
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
        $total_items = count($this->packages);
        
        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to 
         */
        $data = array_slice($this->packages, (($current_page - 1) * $per_page), $per_page);
        
        
        
        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where 
         * it can be used by the rest of the class.
         */
        $this->items = $data;
        
        
        /**
         * REQUIRED. Finally, we build an array to be used by the class for column 
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array(
            $columns,
            $hidden,
            $sortable
        );
        
        
        
        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args(array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $per_page, //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items / $per_page) //WE have to calculate the total number of pages
        ));
        
        
    }
    
    
    
}