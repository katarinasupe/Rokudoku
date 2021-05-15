
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Rokudoku</title>
        <style>
            body {
                background-color: darkgray
            }

            h1 {
                color: crimson
            }
        </style>
    </head>

    <body>
        <form action="rokudoku_game.php" method="POST">
            <h1>Rokudoku!</h1>
            Unesi svoje ime:
            <input type="text" name="name">
            <select name="game_select">
                <option selected disabled></option>
                <option value="game_1" selected>Prva igra</option>
                <option value="game_2">Druga igra</option>
            </select>
            <button type="submit" , name="btn_play">Započni igru!</button>
        </form>
    </body>

    </html>