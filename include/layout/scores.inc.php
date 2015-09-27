<?php

function scoreboard ($scores) {

    echo '
    <table class="team-table table table-striped table-hover">
      <thead>
        <tr>
          <th>#</th>
          <th>',lang_get('team'),'</th>
          <th class="text-center">',lang_get('country'),'</th>
          <th>',lang_get('points'),'</th>
        </tr>
      </thead>
      <tbody>
     ';

    $i = 1;
    foreach($scores as $score) {

        echo '
        <tr>
          <td>',number_format($i++),'</td>
          <td class="team-name">
            <a href="user?id=',htmlspecialchars($score['user_id']),'">
              <span class="team_',htmlspecialchars($score['user_id']),'">
                ',htmlspecialchars($score['team_name']),'
              </span>
            </a>
          </td>
          <td class="text-center">
            ',country_flag_link($score['country_name'], $score['country_code']),'
          </td>
          <td>',number_format($score['score']),'</td>
        </tr>
        ';
    }

    echo '
      </tbody>
    </table>
    ';
}

function get_position_medal ($position, $returnpos = false) {
    switch ($position) {
        case 1:
            return '<img src="'.CONFIG_SITE_URL.'img/award_star_gold_3.png" title="'.lang_get('challenge_solved_first').'" alt="'.lang_get('challenge_solved_first').'" />';
        case 2:
            return '<img src="'.CONFIG_SITE_URL.'img/award_star_silver_3.png" title="'.lang_get('challenge_solved_second').'" alt="'.lang_get('challenge_solved_second').'" />';
        case 3:
            return '<img src="'.CONFIG_SITE_URL.'img/award_star_bronze_3.png" title="'.lang_get('challenge_solved_third').'" alt="'.lang_get('challenge_solved_third').'" />';
    }

    if ($returnpos) {
        return '#'.$position.', ';
    }

    return '';
}