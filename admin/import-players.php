<?php
function import_players($players)
{


    // Add instructions to how to format the csv file
    $instructions = '<p>The csv file must be in the following format:</p>';
    $instructions .= '<p>First Name, Last Name, Position, Club Team, High School, State, Graduating Class, UA Camp, Height, Star Rating, Unique ID</p>';


    echo $instructions;

    echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="POST" enctype="multipart/form-data">';
    // create checkboxes for each column
    echo '<p>Select the columns you want to import:</p>';

    echo '<p>Position: <input type="checkbox" name="position" value="1" checked></p>';
    echo '<p>Club Team: <input type="checkbox" name="club_team" value="1" checked></p>';
    echo '<p>High School: <input type="checkbox" name="high_school" value="1" checked></p>';
    echo '<p>State: <input type="checkbox" name="state" value="1" checked></p>';
    echo '<p>Graduating Class: <input type="checkbox" name="graduating_class" value="1" checked></p>';
    echo '<p>UA Camp: <input type="checkbox" name="ua_camp" value="1" checked></p>';
    echo '<p>Height: <input type="checkbox" name="height" value="1" checked></p>';
    echo '<p>Star Rating: <input type="checkbox" name="star_rating" value="1" checked></p>';

    echo '<input type="hidden" name="action" value="import_players" />';
    echo '<input type="file" name="csv_file" />';
    echo '<input type="submit" name="submit" value="Submit" />';
    echo '</form>';


// If the form has been submitted, process the file and import the data as players
    if (isset($_POST['submit'])) {
        // Get the file name and store it in a variable
        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, "r");
        $row = 0;
        $players = array();
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if ($row > 0) {
                $players[] = array(
                    'first_name' => $data[0],
                    'last_name' => $data[1],
                    'position' => $data[2],
                    'club_team' => $data[3],
                    'recruiting_school' => $data[4],
                    'high_school' => $data[5],
                    'state' => $data[6],
                    'graduating_class' => $data[7],
                    'ua_camp' => $data[8],
                    'height' => $data[9],
                    'star_rating' => $data[10],
                    'unique_id' => $data[11],
                );
            }
            $row++;
        }
        fclose($handle);

        foreach ($players as $player) {

            // if first or last name is empty, skip the player
            if (empty($player['first_name']) || empty($player['last_name'])) {
                continue;
            }

            $player_already_exists = false;

            $existing_player = post_exists($player['first_name'] . ' ' . $player['last_name'],'','','player');

            if ($existing_player) {
                $args = array(
                    'post_type' => 'player',
                    'meta_query' => array(
                        array(
                            'key' => 'unique_id',
                            'value' => $player['unique_id'],
                            'compare' => '=',
                        ),
                    ),
                );
                $query = new WP_Query($args);
                if ($query->have_posts()) {
                    $player_already_exists = true;
                    $existing_player = $query->posts[0]->ID;
                }
                wp_reset_postdata();
            }

            if ($player_already_exists) {
                $player_id = $existing_player;
            } else  {
                $player_id = wp_insert_post(array(
                    'post_title' => $player['first_name'] . ' ' . $player['last_name'],
                    'post_type' => 'player',
                    'post_status' => 'publish'
                ));
            }

            // if position checkbox is checked, add the position to the post
            if (isset($_POST['position'])) {
                $position = get_term_by('name', $player['position'], 'position');
                wp_set_post_terms($player_id, array((int)$position->term_id), 'position', false);
                update_field('position', $player['position'], $player_id);
            }

            // if club_team checkbox is checked, add the club_team to the post
            if (isset($_POST['club_team'])) {
                wp_set_post_terms($player_id, $player['club_team'], 'club_team', false);
            }

            // if recruiting_school checkbox is checked, add the recruiting_school to the post
            if (isset($_POST['recruiting_school'])) {
                wp_set_post_terms($player_id, $player['recruiting_school'], 'recruiting_school', false);
            }

            // if high_school checkbox is checked, add the high_school to the post
            if (isset($_POST['high_school'])) {
                wp_set_post_terms($player_id, $player['high_school'], 'high_school', false);
            }

            // if state checkbox is checked, add the state to the post
            if (isset($_POST['state'])) {
                $state = get_term_by('name', $player['state'], 'state');
                wp_set_post_terms($player_id, array((int)$state->term_id), 'state', false);
                update_field('state', $player['state'], $player_id);
            }

            // if graduating_class checkbox is checked, add the graduating_class to the post
            if (isset($_POST['graduating_class'])) {
                $graduating_class = get_term_by('name', $player['graduating_class'], 'graduating_class');
                wp_set_post_terms($player_id, array((int)$graduating_class->term_id), 'graduating_class', false);
            }

            // if ua_camp checkbox is checked, add the ua_camp to the post
            if (isset($_POST['ua_camp'])) {
                if ($player['ua_camp'] == 1) {
                    update_field('ua_camp', 1, $player_id);
                } else {
                    update_field('ua_camp', 0, $player_id);
                }
            }

            // if height checkbox is checked, add the height to the post
            if (isset($_POST['height'])) {
                if ($player['height'] != '') {
                    update_field('height', $player['height'], $player_id);
                }
            }

            // if star_rating checkbox is checked, add the star_rating to the post
            if (isset($_POST['star_rating'])) {
                update_field('star_rating', intval($player['star_rating']), $player_id);
            }

            update_field('first_name', $player['first_name'], $player_id);
            update_field('last_name', $player['last_name'], $player_id);
            update_post_meta($player_id, 'unique_id', $player['unique_id']);

        }

        echo '<p>Players have been imported.</p>';

        // remove the file from the server
        unlink($file);

    }

    exit();
}


function import_players_admin_action() {




}