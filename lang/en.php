<?php

return [
    // App & Navigation
    'app_title'      => 'Game Tracker',
    'nav_home'       => 'Home',
    'nav_dashboard'  => 'Dashboard',
    'nav_library'    => 'Library',
    'nav_my_entries' => 'My Entries',
    'nav_login'      => 'Login',
    'nav_register'   => 'Register',
    'nav_logout'     => 'Logout',
    'nav_user'       => 'user',
    'footer_made_by' => 'Made by',

    // Generic words
    'notes'          => 'Notes',
    'date'           => 'Date',
    'status'         => 'Status',
    'edit'           => 'Edit',
    'delete'         => 'Delete',
    'cancel'         => 'Cancel',
    'save_changes'   => 'Save Changes',
    'none'           => 'None',
    'hours_short'    => 'hr',

    // Flash messages
    'flash_all_fields_required'   => 'All fields are required.',
    'flash_invalid_email'         => 'Please enter a valid email address.',
    'flash_password_too_short'    => 'Password must be at least 8 characters long.',
    'flash_username_too_short'    => 'Username must be at least 3 characters long.',
    'flash_invalid_image_url'     => 'Please provide a valid image URL.',
    'flash_invalid_release_year'  => 'Release year must be between 1950 and 2050.',
    'flash_session_future_date'   => 'The date cannot be in the future.',
    'flash_date_order_invalid'    => 'The finish date must be after the start date.',
    'flash_username_email_taken'  => 'Username or email already taken.',
    'flash_register_success'      => 'Registered successfully.',
    'flash_invalid_credentials'   => 'Invalid email or password.',
    'flash_login_success'         => 'Login successful.',
    'flash_game_created'          => 'Game created.',
    'flash_game_updated'          => 'Game updated.',
    'flash_game_deleted'          => 'Game deleted.',
    'flash_game_not_found'        => 'Game not found.',
    'flash_entry_added'           => 'Game added to your entries.',
    'flash_entry_updated'         => 'Entry updated.',
    'flash_entry_removed'         => 'Entry removed.',
    'flash_entry_not_found'       => 'Entry not found.',
    'flash_session_added'         => 'Session logged.',
    'flash_session_deleted'       => 'Session deleted.',
    'flash_session_not_found'     => 'Session not found.',
    'flash_invalid_session_inputs' => 'Invalid session data. Check the date and duration.',
    'flash_invalid_status'        => 'Invalid status.',
    'flash_unauthorized'          => 'You do not have permission to do that.',
    'flash_db_error'              => 'Could not save changes. Please try again.',
    'flash_delete_failed'         => 'Could not delete. Please try again.',
    'flash_remove_failed'         => 'Could not remove entry. Please try again.',
    'flash_session_delete_failed' => 'Could not delete session. Please try again.',

    // Index page
    'index_title_1'    => 'Game',
    'index_title_2'    => 'Tracker',
    'index_question'   => 'Do you forget what games you want to play?',
    'index_answer'     => 'Track your games, log your sessions, and never lose your place again.',
    'index_get_started' => 'Get Started',

    // Login and Register
    'username'          => 'Username',
    'email'             => 'Email',
    'password'          => 'Password',
    'choose_name'       => 'Choose a username',
    'email_placeholder' => 'example@email.com',
    'enter_email'       => 'Enter your email',
    'create_account'    => 'Create account',
    'already_registered' => 'Already have an account?',
    'login_here'        => 'Log in here',
    'no_account'        => 'No account yet?',
    'register_now'      => 'Register now',

    // Dashboard
    'welcome'        => 'Welcome,',
    'browse_library' => 'Browse Library',
    'my_entries'     => 'My Entries',
    'overview'       => 'Overview',
    'total_tracked'  => 'Games tracked',
    'games_finished' => 'Games finished',
    'total_time'     => 'Total time played',
    'last_played'    => 'Last played',

    // Game Library page
    'lib_title'              => 'Global Library',
    'lib_subtitle'           => 'All games',
    'lib_search_placeholder' => 'Search by title or genre...',
    'lib_search'             => 'Search',
    'lib_add_game_btn'       => '+ Add Game',
    'lib_no_games_found'     => 'No games found.',
    'lib_clear_search'       => 'Clear search',

    // Game card
    'card_add_to_entries' => 'Add to entries',

    // Game detail page
    'back_to_library' => 'Back to Library',

    // Entries page
    'entries_title'          => 'My Entries',
    'entries_filter_all'     => 'All',
    'entries_filter_playing' => 'Playing',
    'entries_filter_waitlist' => 'Waitlist',
    'entries_filter_finished' => 'Finished',
    'entries_add_game'       => 'Add a game',

    // Entry detail page
    'back_to_entries'    => 'Back to Entries',
    'my_rating'          => 'My Rating',
    'time_tracked'       => 'Time Tracked',
    'started'            => 'Started',
    'finished'           => 'Finished',
    'btn_edit_entry'     => 'Edit entry',
    'btn_remove_entry'   => 'Remove entry',
    'no_notes'           => 'No notes written yet.',
    'sessions'           => 'Sessions',
    'btn_log_session'    => '+ Log Session',
    'no_sessions'        => 'No sessions recorded yet.',
    'remove_session'     => 'Remove session',
    'not_rated'          => 'Not rated',
    'table_length'       => 'Duration',
    'table_action'       => 'Action',
    'confirm_delete_session' => 'Delete this session?',

    // Status labels
    'status_waitlist' => 'Waitlist',
    'status_playing'  => 'Playing',
    'status_finished' => 'Finished',
    'status_quit'     => 'Quit',

    // Modal - Add / Edit game
    'modal_add_game_title'  => 'Add new game',
    'modal_edit_game_title' => 'Edit game',
    'field_title'           => 'Title',
    'field_genre'           => 'Genre',
    'field_genre_placeholder' => 'e.g. RPG, Action',
    'field_year'            => 'Release year',
    'field_cover_url'       => 'Cover image URL',
    'field_description'     => 'Description',
    'btn_save_game'         => 'Save Game',

    // Modal - Delete game
    'modal_delete_title'   => 'Warning',
    'modal_delete_prompt'  => 'from the database?',
    'btn_confirm_delete'   => 'Confirm delete',

    // Modal - Edit entry
    'modal_edit_entry_title' => 'Edit entry',
    'field_rating'           => 'Rating (1-5)',
    'field_rating_none'      => 'No rating',
    'field_date_started'     => 'Date started',
    'field_date_finished'    => 'Date finished',

    // Modal - Remove entry
    'modal_remove_title'   => 'Remove',
    'modal_remove_prompt'  => 'from your entries?',
    'btn_remove'           => 'Remove',

    // Modal - Log session
    'modal_log_session_title'       => 'Log Session',
    'field_duration'                => 'Minutes played',
    'field_notes_optional'          => 'Notes (optional)',
    'field_notes_placeholder'       => 'What did you do this session?',
    'btn_log_session_submit'        => 'Log Session',
];
