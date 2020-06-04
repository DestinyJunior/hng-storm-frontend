<?php

//Get scripts
$folder = 'testScripts';
$files = scandir($folder);

//Check if the script exists and set its command
function getScripts($files, $folder)
{
    $extensions = [
        'js' => 'node',
        'php' => 'php',
        'py' => 'python3',
    ];

    foreach ($files as $file) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        // var_dump($ext);
        if (array_key_exists($ext, $extensions)) {
            $scripts[] = ['name' => "$folder/" . $file, 'command' => $extensions[$ext], 'filename' => $file];
        }
    }

    return $scripts;
};

$scripts = getScripts($files, $folder);
$totalScripts = count($scripts);
$totalScript = 0;
$totalPassed = 0;

//Loop through the scripts, execute and store it output in an array
foreach ($scripts as $key => $script) {
    if (file_exists($scripts[$key]['name'])) {
        $read = exec("{$scripts[$key]['command']} {$scripts[$key]['name']}");
        $content[] = ['output' => $read, 'filename' => $scripts[$key]['name']];
    }
}

function stripbrackets($data)
{
    $data = preg_replace('/\[/i', '', $data);

    $data = preg_replace('/\]/i', '', $data);
    return $data;

}

$members = [];
$messages = [];

$re = '/^Hello World, this is (?<first>\[\w+\])? (?<last>\[\w+\])? with HNGI7 ID (?<id>\[HNG-\d+\])? using (?<language>\[\w+\])? for stage 2 task. /i';

foreach ($content as $key => $data) {
    $output = $content[$key]['output'];
    $str = $output;
    $email = explode(" ", $str);
    $email = array_pop($email);
    $email = trim($email);
    $filename = $content[$key]['filename'];
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);       
        if ($matches) {
            foreach ($matches as $match) {
                $totalPassed++;
                $userData = $match[0];

                $data = preg_replace('/\[/i', '', $userData);
                $trimData = explode(".",trim($data));
                $data = preg_replace('/\]/i', '', $trimData[0]);

                $fullname = $match['first'] . ' ' . $match['last'];

                $fullname = preg_replace('/\[/i', '', $fullname);

                $fullname = preg_replace('/\]/i', '', $fullname);

                $messages[] = ['id' => $match['id'], 'message' => $data, 'name' => $fullname, 'pass' => true, 'filename' => $filename];

                $members[] = [
                    'output' => $data,
                    'id' => stripbrackets($match['id']), 
                    'firstname' => stripbrackets($match['first']), 
                    'lastname' => stripbrackets($match['last']), 
                    'email' => $email, 
                    'language' => stripbrackets($match['language']), 
                    'filename' => $filename, 
                    'status' => 'Pass'
                ];
            }
        } else {
            $userMessage = str_replace($email, '', $output);
            $userMessage = preg_replace('/\[/', '', $userMessage);
            $userMessage = preg_replace('/\]/', '', $userMessage);
            $messages[] = ['id' => 'Poorly Formated File', 'message' => $userMessage, 'pass' => false, "filename" => $filename];
            $members[] = [
                    'output' => $data,
                    'id' => stripbrackets($match['id']), 
                    'firstname' => stripbrackets($match['first']), 
                    'lastname' => stripbrackets($match['last']), 
                    'email' => $email, 
                    'language' => stripbrackets($match['language']), 
                    'filename' => $filename, 
                    'status' => 'Fail'
                ];
        }
    } else {
        $failed = "You did not provide a valid email address. Your String must return an email";
        $messages[] = ['id' => 'No Email Returned', 'message' => $failed, 'pass' => false, 'filename' => $filename];
        $members[] = [
                    'output' => $data,
                    'id' => 'Invalid', 
                    'firstname' => 'Invalid', 
                    'lastname' => 'Invalid', 
                    'email' => $email, 
                    'language' => 'Invalid', 
                    'filename' => $filename, 
                    'status' => 'Fail'
                ];
    }
}

if ($_SERVER['QUERY_STRING'] === 'json') {
    header('Content-Type: application/json');
    if(ob_get_level()) ob_start();

    $members = json_encode($members);    
    echo $members;
    ob_flush();
    flush();
    exit;
}

$total = count($members);
// echo $totalPassed;
// echo $totalScripts;

// var_dump($message);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@900&display=swap" rel="stylesheet">
  <title>Document</title>
</head>

<body>
<header>
    <h1>Team Storm</h1>
    <div class='leads-container'>
      <div>
        <p>@Esi</p>
        <p>Team Lead</p>
      </div>
      <div>
        <p>@__josh__</p>
        <p>Frontend Lead</p>
      </div>
      <div>
        <p>@Aj</p>
        <p>DevOps</p>
      </div>
    </div>
  </header>

  <main class='grid-container'>
    <aside>
      <h3>Team List</h3>
      <button>JSON OUTPUT</button>
    </aside>

    <section>
      <div class='flex'>
        <div class="list-color"></div>
        <h5>Team List</h5>
      </div>
      <div class='graphics-cards-container'>
        <div class='card-one'>
          <p>Total Submissions</p>
          <p><?=$totalScripts?></p></p>
          <div id="foo">
          </div>
          <div id="bar">
          </div>
        </div>
        <div class='card-two'>
          <p>Total Passed</p>
          <p><?=$totalPassed?></p>
          <div id="foo">
          </div>
          <div id="bar">
          </div>
        </div>
        <div class='card-three'>
          <p>Failed</p>
          <p> <?=$totalScripts - $totalPassed?></p>
          <div id="foo">
          </div>
          <div id="bar">
          </div>
        </div>
      </div>
      <input type="text" id="input" onkeyup="myFunction()" placeholder="Search for names..">
      <div class='table-container'>
        <table class="pagination" data-pagecount="4" id="table">


        <thead>
            <tr>
              <th>HNGID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Message</th>
              <th>Status</th>
            </tr>
          </thead>

   
        <tbody>
            <!-- use bg-green-500 class for passed -->
            <?php if(ob_get_level()) ob_start(); ?>
            <?php foreach ($messages as $output): ?>

            <?php if ($output['pass'] === true): ?>

            <tr class="bg-green-500">
                <td class="border px-4 py-2"><?=$output['id']?></td>
                <td class="border px-4 py-2"><?=$output['name']?></td>
                <td class="border px-4 py-2"><?=$output['message']?></td>
                <td>
                <div class='pass'>Pass</div>
              </td>
            </tr>
            <?php elseif ($output['pass'] === false): ?>
            <tr class="bg-red-500">
                <td class="border px-4 py-2"><?=$output['id']?></td>
                <td class="border px-4 py-2"><?="Incorrect formatting from " . $output['filename']?></td>
                <td class="border px-4 py-2"><?=$output['message']?></td>
                <td>
                <div class='fail'>Fail</div>
              </td>
            </tr>

            <?php endif;?>



            <!-- use bg-red-500 class for passed -->

        </tbody>
        <?php ob_flush(); flush(); ?>
        <?php endforeach;?>
    </table>




          


        <div id='insert'></div>
      </div>
    </section>


  </main>


  <script src="script.js">

  </script>

  <script src="js/script.js"></script>

  
</body>

</html>


