<?php
function quality_project_customizer( $wp_customize ) {

//Home project Section
	$wp_customize->add_panel( 'quality_project_setting', array(
		'priority'       => 700,
		'capability'     => 'edit_theme_options',
		'title'      => __('Project settings', 'quality'),
	) );
	
	$wp_customize->add_section(
        'project_section_settings',
        array(
            'title' => __('Project settings','quality'),
			'panel'  => 'quality_project_setting',)
    );
	
	
	//Show and hide Project section
	$wp_customize->add_setting(
	'quality_pro_options[home_projects_enabled]'
    ,
    array(
        'default' => true,
		'capability'     => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'type' => 'option',
    )	
	);
	$wp_customize->add_control(
    'quality_pro_options[home_projects_enabled]',
    array(
        'label' => __('Enable home project section','quality'),
        'section' => 'project_section_settings',
        'type' => 'checkbox',
    )
	);
	
	// //Project Title
	$wp_customize->add_setting(
    'quality_pro_options[project_heading_one]',
    array(
        'default' => __('Featured portfolio project','quality'),
		'capability'     => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'type' => 'option',
		)
	);	
	$wp_customize->add_control('quality_pro_options[project_heading_one]',array(
    'label'   => __('Section Heading','quality'),
    'section' => 'project_section_settings',
	 'type' => 'text',)  );	
	 
	//Project Description 
	 $wp_customize->add_setting(
    'quality_pro_options[project_tagline]',
    array(
        'default' => 'aecenas sit amet tincidunt elit. Pellentesque habitant morbi tristique senectus et netus et Nulla facilisi.',
		'capability'     => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'type' => 'option',
		)
	);	
	$wp_customize->add_control( 'quality_pro_options[project_tagline]',array(
    'label'   => __('Section Tagline','quality'),
    'section' => 'project_section_settings',
	 'type' => 'text',)  );
	 
	 
	 
	 $wp_customize->add_section(
        'project_one_section_settings',
        array(
            'title' => __('Project one','quality'),
			'panel'  => 'quality_project_setting',)
    );
	
	
	
	
	//Project one Title
	$wp_customize->add_setting(
	'quality_pro_options[project_one_title]', array(
        'default'        => 'Lorem Ipsum',
        'capability'     => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'type' => 'option',
    ));
    $wp_customize->add_control('quality_pro_options[project_one_title]', array(
        'label'   => __('Title', 'quality'),
        'section' => 'project_one_section_settings',
		'priority'   => 150,
		'type' => 'text',
    ));
	
	//Project one image
	$wp_customize->add_setting( 'quality_pro_options[project_one_thumb]',array('default' => get_template_directory_uri().'/images/project_thumb.png',
	'type' => 'option','sanitize_callback' => 'esc_url_raw',));
 
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'quality_pro_options[project_one_thumb]',
			array(
				'label' => __('Image','quality'),
				'section' => 'example_section_one',
				'settings' =>'quality_pro_options[project_one_thumb]',
				'section' => 'project_one_section_settings',
				'type' => 'upload',
			)
		)
	);
	
	//Project Two
	$wp_customize->add_section(
        'project_two_section_settings',
        array(
            'title' => __('Project two','quality'),
			'panel'  => 'quality_project_setting',)
    );
	
	//Project Two Title
	$wp_customize->add_setting(
	'quality_pro_options[project_two_title]', array(
        'default'        => 'Postao je popularan',
        'capability'     => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'type' => 'option',
    ));
    $wp_customize->add_control('quality_pro_options[project_two_title]', array(
        'label'   => __('Title', 'quality'),
        'section' => 'project_two_section_settings',
		'priority'   => 150,
		'type' => 'text',
    ));
	
	//Project two image
	$wp_customize->add_setting( 'quality_pro_options[project_two_thumb]',array('default' => get_template_directory_uri().'/images/project_thumb1.png','type' => 'option','sanitize_callback' => 'esc_url_raw',));	
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'quality_pro_options[project_two_thumb]',
			array(
				'label' => __('Image','quality'),
				'section' => 'example_section_one',
				'settings' =>'quality_pro_options[project_two_thumb]',
				'section' => 'project_two_section_settings',
				'type' => 'upload',
			)
		)
	);
	
	//Project Three section
	$wp_customize->add_section(
        'project_three_section_settings',
        array(
            'title' => __('Project three','quality'),
			'panel'  => 'quality_project_setting',)
    );
	
	//Project Three Title
	$wp_customize->add_setting(
	'quality_pro_options[project_three_title]', array(
        'default'        => 'kojekakve promjene s',
        'capability'     => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'type' => 'option',
    ));
    $wp_customize->add_control('quality_pro_options[project_three_title]', array(
        'label'   => __('Title','quality'),
        'section' => 'project_three_section_settings',
		'priority'   => 150,
		'type' => 'text',
    ));
	
	//Project three image
	$wp_customize->add_setting( 'quality_pro_options[project_three_thumb]',array('default' => get_template_directory_uri().'/images/project_thumb2.png','type' => 'option','sanitize_callback' => 'esc_url_raw',));
 
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'quality_pro_options[project_three_thumb]',
			array(
				'label' => __('Image','quality'),
				'section' => 'example_section_one',
				'settings' =>'quality_pro_options[project_three_thumb]',
				'section' => 'project_three_section_settings',
				'type' => 'upload',
			)
		)
	);
	
	
	//Project Four section
	$wp_customize->add_section(
        'project_four_section_settings',
        array(
            'title' => __('Project four','quality'),
			'panel'  => 'quality_project_setting',)
    );
	
	//Project Four Title
	$wp_customize->add_setting(
	'quality_pro_options[project_four_title]', array(
        'default'        => 'kojekakve promjene s',
        'capability'     => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'type' => 'option',
    ));
    $wp_customize->add_control('quality_pro_options[project_four_title]', array(
        'label'   => __('Title', 'quality'),
        'section' => 'project_four_section_settings',
		'priority'   => 150,
		'type' => 'text',
    ));
	
	//Project Four image
	$wp_customize->add_setting( 'quality_pro_options[project_four_thumb]',array('default' => get_template_directory_uri().'/images/project_thumb3.png','type' => 'option','sanitize_callback' => 'esc_url_raw',));
 
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'quality_pro_options[project_four_thumb]',
			array(
				'label' => __('Image','quality'),
				'section' => 'example_section_one',
				'settings' =>'quality_pro_options[project_four_thumb]',
				'section' => 'project_four_section_settings',
				'type' => 'upload',
			)
		)
	);
}		
	add_action( 'customize_register', 'quality_project_customizer' );
	?>