<?php
function quality_service_customizer( $wp_customize ) {
 
//Service section panel
$wp_customize->add_panel( 'quality_service_options', array(
		'priority'       => 600,
		'capability'     => 'edit_theme_options',
		'title'      => __('Service settings', 'quality'),
	) );

	
	$wp_customize->add_section( 'service_section_head' , array(
		'title'      => __('Section Heading', 'quality'),
		'panel'  => 'quality_service_options',
		'priority'   => 50,
   	) );
	
	
	//Show and hide Service section
	$wp_customize->add_setting(
	'quality_pro_options[service_enable]'
    ,
    array(
        'default' => true,
		'capability'     => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'type' => 'option',
    )	
	);
	$wp_customize->add_control(
    'quality_pro_options[service_enable]',
    array(
        'label' => __('Enable service on homepage','quality'),
        'section' => 'service_section_head',
        'type' => 'checkbox',
    )
	);
	
	
	//Sarvice title
	$wp_customize->add_setting(
    'quality_pro_options[service_title]',
    array(
        'default' => __('Our services','quality'),
		'capability'     => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'type' => 'option'
    )	
	);
	$wp_customize->add_control(
    'quality_pro_options[service_title]',
    array(
        'label' => __('Title','quality'),
        'section' => 'service_section_head',
        'type' => 'text',
    )
	);
	
	$wp_customize->add_setting(
    'quality_pro_options[service_description]',
    array(
        'default' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's",
		'sanitize_callback' => 'sanitize_text_field',
		'type' => 'option'
    )	
	);
	$wp_customize->add_control(
    'quality_pro_options[service_description]',
    array(
        'label' => __('Description','quality'),
        'section' => 'service_section_head',
        'type' => 'text',
		'sanitize_callback' => 'sanitize_text_field',
    )
	);
	
//service section one
	$wp_customize->add_section( 'service_section_one' , array(
		'title'      => __('Service one', 'quality'),
		'panel'  => 'quality_service_options',
		'priority'   => 100,
		'sanitize_callback' => 'sanitize_text_field',
   	) );
	$wp_customize->add_setting(
		'quality_pro_options[service_one_icon]', array(
		 'sanitize_callback' => 'sanitize_text_field',
        'default'        => 'fa fa-shield',
        'capability'     => 'edit_theme_options',
		'type' => 'option',
    ));
	
	$wp_customize->add_control( 'quality_pro_options[service_one_icon]', array(
        'label'   => __('Icon', 'quality'),
		'section' => 'service_section_one',
        'type'    => 'text',
    ));		
		
	$wp_customize->add_setting(
    'quality_pro_options[service_one_title]',
    array(
        'default' => __('Fully responsive','quality'),
		'capability'     => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'type' => 'option'
    )	
	);
	$wp_customize->add_control(
    'quality_pro_options[service_one_title]',
    array(
        'label' => __('Title','quality'),
        'section' => 'service_section_one',
        'type' => 'text',
    )
	);

	$wp_customize->add_setting(
    'quality_pro_options[service_one_text]',
    array(
        'default' => 'Lorem Ipsum which looks reason able. The generated Lorem Ipsum is ',
		 'capability'     => 'edit_theme_options',
		 'sanitize_callback' => 'sanitize_text_field',
		 'type' => 'option'
    )	
	);
	$wp_customize->add_control(
    'quality_pro_options[service_one_text]',
    array(
        'label' => __('Description','quality'),
        'section' => 'service_section_one',
        'type' => 'text',	
    )
);
//Second service

$wp_customize->add_section( 'service_section_two' , array(
		'title'      => __('Service two', 'quality'),
		'panel'  => 'quality_service_options',
		'priority'   => 200,
   	) );


$wp_customize->add_setting(
    'quality_pro_options[service_two_icon]',
    array(
        'type' =>'option',
		'default' => 'fa fa-tablet',
		 'capability'     => 'edit_theme_options',
		 'sanitize_callback' => 'sanitize_text_field',
		 
    )	
);
$wp_customize->add_control(
    'quality_pro_options[service_two_icon]',
    array(
        'label' => __('Icon','quality'),
        'section' => 'service_section_two',
        'type' => 'text',
    )
);

$wp_customize->add_setting(
    'quality_pro_options[service_two_title]',
    array(
        'default' => __('SEO friendly','quality'),
		'capability'     => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'type' => 'option',
    )	
);
$wp_customize->add_control(
    'quality_pro_options[service_two_title]',
    array(
        'label' => __('Title' ,'quality'),
        'section' => 'service_section_two',
        'type' => 'text',
    )
);

$wp_customize->add_setting(
    'quality_pro_options[service_two_text]',
    array(
        'default' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consec tetur adipisicing elit dignissim dapib tumst.',
		 'capability'     => 'edit_theme_options',
		 'sanitize_callback' => 'sanitize_text_field',
		 'type' => 'option',
    )	
);
$wp_customize->add_control(
		'quality_pro_options[service_two_text]',
		array(
        'label' => __('Description','quality'),
        'section' => 'service_section_two',
        'type' => 'text',
    )
);
//Third Service section
$wp_customize->add_section( 'service_section_three' , array(
		'title'      => __('Service three', 'quality'),
		'panel'  => 'quality_service_options',
		'priority'   => 300,
   	) );


$wp_customize->add_setting(
    'quality_pro_options[service_three_icon]',
    array(
        'default' => 'fa fa-edit',
		'capability'     => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'type' => 'option',
		
    )	
);
$wp_customize->add_control(
'quality_pro_options[service_three_icon]',
    array(
        'label' => __('Icon','quality'),
        'section' => 'service_section_three',
        'type' => 'text',
		
    )
);

$wp_customize->add_setting(
    'quality_pro_options[service_three_title]',
    array(
        'default' => __('Easy customization','quality'),
		'capability'     => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'type' =>'option',
    )	
);
$wp_customize->add_control(
    'quality_pro_options[service_three_title]',
    array(
        'label' => __('Title','quality'),
        'section' => 'service_section_three',
        'type' => 'text',
    )
);

$wp_customize->add_setting(
    'quality_pro_options[service_three_text]',
    array(
        'default' => 'fLorem Ipsum which looks reason able. The generated Lorem Ipsum is t.',
		'capability'     => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'type' =>'option',
    )	
);
$wp_customize->add_control(
    'quality_pro_options[service_three_text]',
    array(
        'label' => __('Description','quality'),
        'section' => 'service_section_three',
        'type' => 'text',
    )
);
//Four Service section

$wp_customize->add_section( 'service_section_four' , array(
		'title'      => __('Service four', 'quality'),
		'panel'  => 'quality_service_options',
		'priority'   => 400,
   	) );

$wp_customize->add_setting(
    'quality_pro_options[service_four_icon]',
    array(
        'default' => 'fa fa-star-half-o',
		'capability'     => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'type' =>'option',
    )	
);
$wp_customize->add_control(
    'quality_pro_options[service_four_icon]',
    array(
        'label' => __('Icon','quality'),
        'section' => 'service_section_four',
        'type' => 'text',
    )
);

$wp_customize->add_setting(
    'quality_pro_options[service_four_title]',
    array(
        'default' => __('Well documentation','quality'),
		'capability'     => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'type' => 'option'
    )	
);
$wp_customize->add_control(
    'quality_pro_options[service_four_title]',
    array(
        'label' => __('Title','quality'),
        'section' => 'service_section_four',
        'type' => 'text',
    )
);

$wp_customize->add_setting(
   'quality_pro_options[service_four_text]',
    array(
        'default' => 'Lorem Ipsum which looks reason able. The generated Lorem Ipsum is-o.',
		'capability'     => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'type' => 'option'
    )	
);
$wp_customize->add_control(
    'quality_pro_options[service_four_text]',
    array(
        'label' => __('Description','quality'),
        'section' => 'service_section_four',
        'type' => 'text',
		'sanitize_callback' => 'sanitize_text_field',
    )
);
}
add_action( 'customize_register', 'quality_service_customizer' );
?>