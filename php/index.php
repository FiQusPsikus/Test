<html>
    <head>
     <script
        src="https://code.jquery.com/jquery-2.2.4.js"
        integrity="sha256-iT6Q9iMJYuQiMWNd9lDyBUStIq/8PuOW33aOqmvFpqI="
        crossorigin="anonymous"></script>
    </head>

    <body>
        <div style="display:flex">
            <div>
                <?php
                    $array = array("Rodzaj","Gatunek","Asd"); // <- tu se dopisz kategorie xD 

                    echo '<h2>Węzły</h2>';

                    foreach($array as $nodeType){
                        $type = $nodeType;
                        $data = json_decode(file_get_contents("http://localhost:3000/listAllByType?type=".$type), TRUE);
                        echo "<b>".$type.":</b> ";
                        foreach ($data as $key => $record){
                            $name = $record['_fields'][0]['properties']['name'];
                            echo '<div onclick="SubFormDel(\''.$name.'\',\''.$type.'\')">'.$name.',</div> ';
                        }
                        echo '</br>';
                    }
                ?>
            </div>
            <div style="margin-left:100px;">
                <?php
                    echo '<h2>Relacje</h2>';
                    foreach($array as $nodeType){
                        $type = $nodeType;
                        $data = json_decode(file_get_contents("http://localhost:3000/listAllByType?type=".$type), TRUE);
                        echo "<b>".$type.":</b><br> ";
                        foreach($data as $key => $record){
                            $name = $record['_fields'][0]['properties']['name'];
                            $name = str_replace(" ","%20",$name);
                            $data1 = json_decode(file_get_contents("http://localhost:3000/searchRelation?type=".$type."&name=".$name), TRUE);
                            foreach($data1 as $data1){
                                $name = str_replace("%20"," ",$name);
                                echo $name.'-['.$data1['_fields'][0]['type'].']->'.$data1['_fields'][1]['properties']['name'].'<br>';
                            }
                        }
                    }
                ?>
            </div>
            <div style="margin-left:200px;">

                    <b>Dodawanie bloczku:</b> <br>
                Przeładować po dodaniu ?<input type="checkbox" id="reload">
                <form style="margin-top:20px;" id="myForm">
                    Kategoria: <input type="text" name="nodeName"><br>
                    Właściwości:</br>
                    name: <input type="text" name="name"> <br>
                    Nazwa pola: Wartość pola:<br>
                </form>

                <button onclick="SubForm()">Dodaj</button>
                <button onclick="addVal()">Dodaj pole</button>

                <br>
                    <br/>
                    <b>Dodawanie relacji:</b>
                <form style="margin-top:20px;" id="myFormRel">
                    Kategoria 1: <input type="text" name="Rel1"><br>
                    Nazwa obiektu 1: <input type="text" name="rel1Name"><br>
                    -[<input type="text" name="typeRelation">]-><br>
                    Kategoria 2: <input type="text" name="Rel2"><br>
                    Nazwa obiektu 2: <input type="text" name="rel2Name"><br>
                    <button onclick="SubFormRel()">Dodaj</button>
                </form>
            </div>
        </div>

        <script>
            var i = 1;
            var reload = document.getElementById("reload").checked;
            function SubForm (){
                var data = $('#myForm').serialize();
                $.ajax({
                    url: 'http://localhost:3000/addNode',
                    type: 'post',
                    data: data,
                     success: function(){
                        if(document.getElementById('reload').checked) {
                            location.reload();
                        }
                    }
                });
            }

            function SubFormRel(){
                $.ajax({
                    url: 'http://localhost:3000/addRelation',
                    type: 'post',
                    data: $('#myFormRel').serialize(),
                    success: function(){
                        location.reload();
                    }
                });
            }

            function SubFormDel(name, type){
                var namee = name;
                var typee = type;
                $.ajax({
                    url: 'http://localhost:3000/deleteItem',
                    type: 'post',
                    data: {
                        name: namee,
                        type: typee
                    },
                    success: function(){
                        location.reload();
                    }
                });
            }

            function addVal(){
                var container = document.getElementById("myForm");

                var input = document.createElement('input');
                input.type="text";
                input.name="NodeValName";
                container.appendChild(input);
                
                var input1 = document.createElement('input');
                input1.type="text";
                input1.name="NodeVal";
                container.appendChild(input1);

                container.appendChild(document.createElement("br"));
            }

        </script>
    </body>

</html>