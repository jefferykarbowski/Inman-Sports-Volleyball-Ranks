<?php
function import_players($players)
{


    // Add instructions to how to format the csv file
    $instructions = '<p>The csv file must be in the following format:</p>';
    $instructions .= '<p>First Name, Last Name, Height, Position, State, High School, Graduating Class, Club Team';


    echo $instructions;

    echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="POST" enctype="multipart/form-data">';
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
                    'height' => $data[2],
                    'position' => $data[3],
                    'state' => $data[4],
                    'high_school' => $data[5],
                    'graduating_class' => $data[6],
                    'club_team' => $data[7]
                );
            }
            $row++;
        }
        fclose($handle);

        foreach ($players as $player) {
            $player_id = wp_insert_post(array(
                'post_title' => $player['first_name'] . ' ' . $player['last_name'],
                'post_type' => 'player',
                'post_status' => 'publish'
            ));
            update_post_meta($player_id, 'height', $player['height']);
            // add the position, state, high_school, graduating_class, and club_team as a taxonomy terms
            wp_set_object_terms($player_id, $player['position'], 'position', false);
            wp_set_object_terms($player_id, $player['state'], 'state', false);
            wp_set_object_terms($player_id, $player['high_school'], 'high_school', false);
            wp_set_object_terms($player_id, $player['graduating_class'], 'graduating_class', false);
            wp_set_object_terms($player_id, $player['club_team'], 'club_team', false);

        }

        echo '<p>Players have been imported.</p>';

        // remove the file from the server
        unlink($file);

    }

    exit();
}


function import_players_admin_action() {




}