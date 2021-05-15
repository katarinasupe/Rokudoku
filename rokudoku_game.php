<?php

session_start();
//debug();

//-----------------ODABIR IGRE-------------------
if(!(isset($_SESSION['start']))){


    if(isset($_POST['game_select'])){
        $game_select = $_POST['game_select'];
        $_SESSION['start'] = [[]];

        for ($row = 0; $row  < 6; ++$row) {
            $_SESSION['start'][$row] = [];
            for ($col = 0; $col < 6; ++$col) {
                $_SESSION['start'][$row][$col] = '';
            }
        }

        switch ($game_select) {
            case 'game_1':
                $_SESSION['start'][0][2] = 4;
                $_SESSION['start'][1][3] = 2;
                $_SESSION['start'][1][4] = 3;
                $_SESSION['start'][2][0] = 3;
                $_SESSION['start'][2][4] = 6;
                $_SESSION['start'][3][1] = 6;
                $_SESSION['start'][3][1] = 6;
                $_SESSION['start'][3][5] = 2;
                $_SESSION['start'][4][1] = 2;
                $_SESSION['start'][4][2] = 1;
                $_SESSION['start'][5][3] = 5;
                break;
            case 'game_2':
                $_SESSION['start'][0][0] = 1;
                $_SESSION['start'][0][2] = 3;
                $_SESSION['start'][0][4] = 4;
                $_SESSION['start'][1][1] = 6;
                $_SESSION['start'][1][3] = 2;
                $_SESSION['start'][1][5] = 3;
                $_SESSION['start'][2][1] = 4;
                $_SESSION['start'][2][3] = 3;
                $_SESSION['start'][2][5] = 1;
                $_SESSION['start'][3][0] = 3;
                $_SESSION['start'][3][2] = 2;
                $_SESSION['start'][3][3] = 4;
                $_SESSION['start'][3][4] = 6;
                $_SESSION['start'][4][0] = 2;
                $_SESSION['start'][4][2] = 1;
                $_SESSION['start'][4][4] = 5;
                $_SESSION['start'][5][1] = 5;
                $_SESSION['start'][5][3] = 1;
                $_SESSION['start'][5][5] = 2;

                break;
            default:
                break;
        }
    }


}


//--------------------PROVJERA IMENA I INICIJALIZACIJA IGRE--------------------
if (isset($_POST['name'])) {
    //$name = $_POST['name'];
    if (isNameOk($_POST['name'])) {
        if (!isset($_SESSION['name'])) {
            $_SESSION['name'] = $_POST['name'];
        }

        // $_SESSION['name'] = $name;
        // $_SESSION['counter'] = 0;
        initialize_game();
    } else {
        echo '<br><a href = "rokudoku.php">Ponovno unesi ime!</a>';
        return 0;
    }
}


//--------------------PROVJERA ODABIRA RADIO BUTTONA--------------------
if (isset($_POST['choice'])) {
    if ($_POST['choice'] === 'input' && isset($_POST['input_row']) && isset($_POST['input_col'])) {
        $number = $_POST['number'];
        $row_input = $_POST['input_row'];
        $col_input = $_POST['input_col'];
        if (isNumberOk($number)) {
            resolve_input($number, $row_input, $col_input);
        } else
            echo "Unesite broj od 1 do 6.";
    }


    else if ($_POST['choice'] === 'erase' && isset($_POST['erase_row']) && isset($_POST['erase_col'])) {
        $row_erase = $_POST['erase_row'];
        $col_erase = $_POST['erase_col'];
        erase_input($row_erase, $col_erase);
    }


    else if ($_POST['choice'] === 'restart') {

        $_SESSION['game'] = $_SESSION['start'];
        $_SESSION['counter'] = 0;

        for ($row = 0; $row  < 6; ++$row) {
            $_SESSION['red'][$row] = [];
            $_SESSION['blue'][$row] = [];
            for ($col = 0; $col < 6; ++$col) {
                $_SESSION['blue'][$row][$col] = '';
                $_SESSION['red'][$row][$col] = '';
            }
        }
    }

    else{
        echo "Ispravno ispunite formu za unos/brisanje broja.";
    }
}

//--------------------CRTANJE IGRE--------------------
if(isset($_SESSION['start'])){
    draw_a_game();
}

//--------------------FUNKCIJE--------------------

function isNumberOk($num)
{

    return preg_match('/^[1-6]{1}$/', $num);
}

function isNameOk($name)
{

    return preg_match('/^[A-Za-z]{1,20}$/', $name);
}

function erase_input($row_erase, $col_erase)
{

    $row_index = (int) $row_erase - 1;
    $col_index = (int) $col_erase - 1;
    for ($row = 0; $row  < 6; ++$row) {
        for ($col = 0; $col < 6; ++$col) {
            if (($_SESSION['start'][$row][$col] !== '') || ($_SESSION['game'][$row][$col] === '')) {
                if (($row === $row_index) && ($col === $col_index)) {
                    echo '<br>';
                    echo "Na tom polju ne možete brisati!";
                    return 0;
                }
            }
        }
    }
  
    $_SESSION['counter']++;
    $_SESSION['game'][$row_index][$col_index] = '';
    $_SESSION['red'][$row_index][$col_index] = '';
    $_SESSION['blue'][$row_index][$col_index] = '';

    get_color();

}

function get_block($row, $col)
{
    if ((intdiv($col, 3) === 0) && (intdiv($row, 2) === 0)) {
        return 1;
    }
    if ((intdiv($col, 3) === 1) && (intdiv($row, 2) === 0)) {
        return 2;
    }
    if ((intdiv($col, 3) === 0) && (intdiv($row, 2) === 1)) {
        return 3;
    }
    if ((intdiv($col, 3) === 1) && (intdiv($row, 2) === 1)) {
        return 4;
    }
    if ((intdiv($col, 3) === 0) && (intdiv($row, 2) === 2)) {
        return 5;
    }
    if ((intdiv($col, 3) === 1) && (intdiv($row, 2) === 2)) {
        return 6;
    }
}


function resolve_input( $number, $row_input, $col_input ){

    $row_index = (int) $row_input - 1;
    $col_index = (int) $col_input - 1;
    $input_block = get_block($row_index, $col_index);

    //ako imas isti pokusaj, ne radi nista
    if ((int)$_SESSION['red'][$row_index][$col_index] === $number) return 0;
    if ((int)$_SESSION['blue'][$row_index][$col_index] === $number) return 0;

    //ako pokusavas unijeti broj na mjesto koje je boldano, ne mozes
    for ($row = 0; $row  < 6; ++$row) {
        for ($col = 0; $col < 6; ++$col) {
            if (($_SESSION['start'][$row][$col]) !== '') { //ako se tu nalazi neki broj
                if (($row === $row_index) && ($col === $col_index)) {
                    echo '<br>';
                    echo "Krivi unos! Pokušaj unosa na zabranjeno mjesto.";
                    return 0;
                }
            }
        }
    }

    $_SESSION['game'][$row_index][$col_index] = $number;
    $_SESSION['counter']++;

    get_color();

    isDone();
}

function get_color(){

    for ($row = 0; $row  < 6; ++$row) {
        for ($col = 0; $col < 6; ++$col) {
            $current_block = get_block($row, $col);
            if(isDuplicate( $row, $col, $_SESSION['game'][$row][$col], $current_block ) ){
                if( $_SESSION['start'][$row][$col] === ''){
                    $_SESSION['blue'][$row][$col] = '';
                    $_SESSION['red'][$row][$col] = $_SESSION['game'][$row][$col];
                }
            }
            else{
                if( $_SESSION['start'][$row][$col] === ''){
                    $_SESSION['red'][$row][$col] = '';
                    $_SESSION['blue'][$row][$col] = $_SESSION['game'][$row][$col];
                }
            }
        }
    }
}

function isDuplicate( $row_index, $col_index, $number, $input_block){

    if( duplicate_in_row( $row_index, $col_index, $number ) || duplicate_in_col( $row_index, $col_index, $number ) || duplicate_in_block($row_index, $col_index, $number, $input_block)){
        return TRUE;
    }
    else return FALSE;

}


function duplicate_in_block($row_index, $col_index, $number, $input_block){

    $duplicate = FALSE;
    for ($row = 0; $row  < 6; ++$row) {

        for ($col = 0; $col < 6; ++$col) {

            //gledam isti blok kao od broja koji zelim unijeti
            if (get_block($row, $col) === $input_block) {

            //gledam sve pozicije osim onih gdje je broj koji unosim 
                if( ($row_index !== $row) && ($col_index !== $col) ){

                    //ako ima duplikata
                    if( (int)$_SESSION['game'][$row][$col] === (int)$number ){

                        $duplicate = TRUE;

                        //ako nije boldani, onda postaje crveni
                        if($_SESSION['start'][$row][$col] === ''){

                            $_SESSION['red'][$row][$col] = $_SESSION['game'][$row][$col];
                            $_SESSION['blue'][$row][$col] = '';

                        }
                    }

                }
            }
        }
    }       
}

function duplicate_in_col($row_index, $col_index, $number){

    $duplicate = FALSE;

    for( $i = 0; $i < 6; $i++ ){
        //idi po svim retcima u stupcu osim onog gdje stavljam broj
        if( $i !== $row_index ){
            //kad naletis na duplikat
            if( (int)$_SESSION['game'][$i][$col_index] === (int)$number ){
                $duplicate = TRUE;
                //a da nije boldan
                if( $_SESSION['start'][$i][$col_index] === ''){
                    //zacrveni ga
                    $_SESSION['red'][$i][$col_index] = $_SESSION['game'][$i][$col_index];
                    $_SESSION['blue'][$i][$col_index] = '';
                }
            }
        }
    }

    return $duplicate;
}

function duplicate_in_row( $row_index, $col_index, $number ){

    $duplicate = FALSE;

    for( $i = 0; $i < 6; $i++ ){
        if( $i !== $col_index ){
            if( (int)$_SESSION['game'][$row_index][$i] === (int)$number ){
                $duplicate = TRUE;
                if( $_SESSION['start'][$row_index][$i] === ''){
                    $_SESSION['red'][$row_index][$i] = $_SESSION['game'][$row_index][$i];
                    $_SESSION['blue'][$row_index][$i] = '';
                }
            }
        }
    }

    return $duplicate;
}


function isDone()
{

    for ($row = 0; $row  < 6; ++$row) {
        for ($col = 0; $col < 6; ++$col) {
            if ($_SESSION['game'][$row][$col] === $_SESSION['red'][$row][$col]) {
                return 0;
            }
            if ($_SESSION['game'][$row][$col] === '') {
                return 0;
            }
        }
    }

    writeCard();
}

function writeCard()
{   
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Rokudoku</title>
        <style>
            body {
                background-color: darkgray;
            }

            h1 {
                color: crimson;
            }

            a {
                color: crimson;
            }
        </style>
    </head>
    <body>
        <h1>Bravo <?php echo $_SESSION['name']; ?>! Uspješno ste riješili igru Rokudoku!</h1>    
        <br>
        <a href = "rokudoku.php">Nova igra</a>
    </body>
    </html>

<?php

    session_unset();
    session_destroy(); 

    return 0;
}


function initialize_game()
{
    $_SESSION['counter'] = 0;
    $_SESSION['game'] = [[]];
    $_SESSION['red'] = [[]];
    $_SESSION['blue'] = [[]];

    for ($row = 0; $row  < 6; ++$row) {
        $_SESSION['game'][$row] = [];
        $_SESSION['blue'][$row] = [];
        $_SESSION['red'][$row] = [];
        for ($col = 0; $col < 6; ++$col) {
            $_SESSION['game'][$row][$col] = '';
            $_SESSION['blue'][$row][$col] = '';
            $_SESSION['red'][$row][$col] = '';
        }
    }

    $_SESSION['game'] = $_SESSION['start'];
}


function draw_a_game()
{ ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Rokudoku</title>
        <style>
            body {
                background-color: darkgray;
            }

            h1 {
                color: crimson;
            }

            table {
                border: 6px solid #555555;
                background-color: #FFFFFF;
                width: 420px;
                height: 420px;
                text-align: center;
                border-collapse: collapse;
            }

            table td {
                border: 2px solid #000000;
                width: 70px;
                height: 70px;
                /* padding: 2px 4px;*/
                font-size: 25px;
                /*font-weight: bold;*/
                color: #000000;
            }

            tr:nth-of-type(2n) td {
                border-bottom: 5px solid #000000;
            }

            td:nth-child(3) {
                border-right: 5px solid #000000;
            }
        </style>
    </head>

    <body>
        <form action=<?php echo $_SERVER['PHP_SELF']; ?> method="POST">
            <h1>Rokudoku!</h1>
            Igrač: <?php echo $_SESSION['name']; ?>
            <br>
            Broj pokušaja: <?php echo $_SESSION['counter']; ?>
            <br>
            <br>
            <table>
                <?php
                for ($row = 0; $row  < 6; ++$row) {
                    echo '<tr>';
                    for ($col = 0; $col < 6; ++$col) {
                        echo '<td>';
                        if ($_SESSION['game'][$row][$col] === $_SESSION['start'][$row][$col]) {
                            echo '<span style = "font-weight: bold;">' . $_SESSION['game'][$row][$col] . '</span>';
                        }

                        if ($_SESSION['game'][$row][$col] === $_SESSION['blue'][$row][$col]) {
                            echo '<span style = "color: blue;">' . $_SESSION['game'][$row][$col] . '</span>';
                        }

                        if ($_SESSION['game'][$row][$col] === $_SESSION['red'][$row][$col]) {
                            echo '<span style = "color: red;">' . $_SESSION['game'][$row][$col] . '</span>';
                        }
                        echo '</td>';
                    }
                    echo '</tr>';
                }
                ?>
            </table>
            <br>
            <br>
            <input type="radio" id="input" name="choice" value="input">
            Unesi broj
            <input type="text" name="number">
            u redak
            <select name="input_row">
                <option selected disabled></option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
            </select>
            i stupac
            <select name="input_col">
                <option selected disabled></option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
            </select>
            <br>
            <br>
            <input type="radio" id="erase" name="choice" value="erase">
            Obriši broj iz retka
            <select name="erase_row">
                <option selected disabled></option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
            </select>
            i stupca
            <select name="erase_col">
                <option selected disabled></option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
            </select>
            <br>
            <br>
            <input type="radio" id="restart" name="choice" value="restart">
            Želim sve ispočetka!
            <br>
            <br>
            <button type="submit" name="btn" value="btn">Izvrši akciju!</button>
        </form>
    </body>

    </html>
<?php
}
?>
