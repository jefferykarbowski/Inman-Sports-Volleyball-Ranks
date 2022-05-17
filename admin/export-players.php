<?php
function export_players($players)
{
    echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="POST" enctype="multipart/form-data">';
    // create checkboxes for each column
    echo '<p>Export Players:</p>';
    echo '<input type="hidden" name="action" value="export_players" />';
    echo '<input type="submit" name="submit" value="Submit" />';
    echo '</form>';

    $csv = "";
    if (isset($_POST['submit'])) {



        $csv = "First Name,Last Name,Position,Club Team,College Commitment,High School,State,Graduating Class,Invited To UA Next Camp,Height,Star Rating,Unique ID\n";


        foreach ($players as $player) {
            $player_meta = get_post_meta($player->ID);
            // add player meta first_name to the csv
            $csv .= get_field('first_name', $player->ID) . ",";
            // add player meta last_name to the csv
            $csv .= get_field('last_name', $player->ID)  . ",";
            // add player taxonomy  position[0] to the csv
            $graduating_class = wp_get_post_terms($player->ID, 'position', array("fields" => "names"));
            $csv .= $graduating_class[0] . ",";
            // add player taxonomy  club_team[0] to the csv
            $graduating_class = wp_get_post_terms($player->ID, 'club_team', array("fields" => "names"));
            $csv .= $graduating_class[0] . ",";
            // add player meta college_commitment taxonomy to the csv
            $graduating_class = wp_get_post_terms($player->ID, 'recruiting_school', array("fields" => "names"));
            $csv .= $graduating_class[0] . ",";
            // add player meta high_school taxonomy to the csv
            $graduating_class = wp_get_post_terms($player->ID, 'high_school', array("fields" => "names"));
            $csv .= $graduating_class[0] . ",";
            // add player meta state taxonomy to the csv
            $graduating_class = wp_get_post_terms($player->ID, 'state', array("fields" => "names"));
            $csv .= $graduating_class[0] . ",";
            // add player meta graduating_class taxonomy to the csv
            $graduating_class = wp_get_post_terms($player->ID, 'graduating_class', array("fields" => "names"));
            $csv .= $graduating_class[0] . ",";
            // add player meta ua_camp to the csv
            $csv .= get_field('ua_camp', $player->ID)  . ",";
            // add player meta height to the csv
            $csv .= get_field('height', $player->ID)  . ",";
            // add player meta star_rating to the csv
            $csv .= get_field('star_rating', $player->ID)  . ",";
            // add player meta unique_id to the csv
            $csv .= sprintf("%08d",get_field('unique_id', $player->ID)) . "\n";

        }

        // remove page headers
        ob_clean();

        // download the csv file
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=players.csv");
        echo $csv;



        exit;

    }
    exit();
}

function generate_unique_id($player_id, $players)
{
    $highest_unique_id = 0;
    foreach ($players as $player) {
        $player_meta = get_post_meta($player->ID);
        if ($player_meta['unique_id'] > $highest_unique_id) {
            $highest_unique_id = $player_meta['unique_id'][0];
        }
    }
    // add 1 to the highest unique id and format to be 8 digits
    $unique_id = sprintf("%08d", $highest_unique_id + 1);
    update_field('unique_id', $unique_id, $player_id);
    return $unique_id;
}


function export_players_admin_action() {





}
