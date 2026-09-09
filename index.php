<?php
if (isset($_GET['nid']) && isset($_GET['dob'])) {
    $nid_input = trim($_GET['nid']);
    $dob_input = trim($_GET['dob']);

    // API URL
    $apiUrl = "https://cyberbdapi.shop/sv/sv2.php?nid=" . urlencode($nid_input) . "&dob=" . urlencode($dob_input);
    
    // cURL ব্যবহার করে নিরাপদ ও দ্রুত ডাটা ফেচিং
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);

    // ডেটা হ্যান্ডলিং
    if (isset($responseData['success']) && ($responseData['success'] === true || $responseData['code'] == 200) && isset($responseData['data'])) {
        $data = $responseData['data'];

        $nameBangla = !empty($data['name']) ? $data['name'] : '-';
        $nameEnglish = !empty($data['nameEn']) ? $data['nameEn'] : '-';
        $pin = !empty($data['pin']) ? $data['pin'] : '-';
        $nationalId = !empty($data['nationalId']) ? $data['nationalId'] : $nid_input;
        $vsl = !empty($data['sl_no']) ? $data['sl_no'] : '-'; 
        $vno = !empty($data['voter_no']) ? $data['voter_no'] : '-'; 
        $vac = !empty($data['voterArea']) ? $data['voterArea'] : (!empty($data['voterAreaCode']) ? $data['voterAreaCode'] : '-');
        $dob = !empty($data['dateOfBirth']) ? $data['dateOfBirth'] : $dob_input;
        $photo = !empty($data['photo']) ? $data['photo'] : 'https://courcenet.my.id/avatar/server.png';
        $gender = !empty($data['gender']) ? $data['gender'] : '-';
        $spouse = !empty($data['spouse']) ? $data['spouse'] : '-';  
        $occupation = !empty($data['occupation']) ? $data['occupation'] : '-';
        $blood = !empty($data['bloodGroup']) ? $data['bloodGroup'] : 'N/A';
        $religion = !empty($data['religion']) ? $data['religion'] : '-';
        $birth = !empty($data['birthPlace']) ? $data['birthPlace'] : (!empty($data['permanentAddress']['district']) ? $data['permanentAddress']['district'] : '-');

        // পিতা ও মাতার তথ্য
        $father = !empty($data['father']) ? $data['father'] : '-';
        $mother = !empty($data['mother']) ? $data['mother'] : '-';

        // ঠিকানা
        $present = !empty($data['presentAddress']['addressLine']) ? $data['presentAddress']['addressLine'] : '-';
        $permanent = !empty($data['permanentAddress']['addressLine']) ? $data['permanentAddress']['addressLine'] : '-';

        // QR Code URL
        $qrPayload = $nameEnglish . ' ' . $nationalId . ' ' . $dob;
        $qrcode = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrPayload);
        $userImg = $photo;
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            "status" => "error",
            "message" => "সার্ভার থেকে সঠিক তথ্য পাওয়া যায়নি অথবা ইনপুট ভুল।"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
} else {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["status" => "error", "message" => "nid or dob parameter is missing"], JSON_UNESCAPED_UNICODE);
    exit;
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?php echo htmlspecialchars($nationalId); ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.1.1/css/all.css">

    <style>
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            box-sizing: border-box;
        }

        @page {
            size: A4;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            font-family: Arial, Helvetica, sans-serif;
            text-align: center;
        }

        .background {
            background-color: transparent;
            position: relative;
            width: 1070px;
            height: 1500px;
            margin: 0 auto;
            overflow: hidden;
        }

        .crane {
            position: absolute;
            top: 0;
            left: 0;
            width: 1070px;
            height: 1500px;
            z-index: 1;
        }

        .bangla {
            font-family: 'Tiro Bangla', Arial, sans-serif !important;
        }

        .label {
            font-size: 18px;
            color: #070707;
            font-weight: 500;
        }

        .value {
            font-size: 16.5px;
            color: #070707;
        }

        @media print {
            body {
                background-color: #fff !important;
            }
            .background {
                width: 1070px;
                height: 1500px;
                transform-origin: top left;
            }
            .no-print {
                display: none !important;
            }
        }

        #present_addr,
        #permanent_addr {
            text-align: left;
            line-height: 1.4;
        }
    </style>
</head>
<body>

<div class="background">
    <!-- ব্যাকগ্রাউন্ড ফ্রেম ছবি -->
    <img class="crane" src="https://courcenet.my.id/avatar/server.png" alt="Server Frame">
    
    <!-- হেডার টেক্সট সমূহ -->
    <div style="position: absolute; left: 30%; top: 8%; width: auto; font-size: 25.5px; color: rgb(255, 224, 0); z-index: 2;"><b>National Identity Registration Wing (NIDW)</b></div>
    <div style="position: absolute; left: 39%; top: 11%; width: auto; font-size: 18px; color: rgb(255, 47, 161); z-index: 2;"><b>Select Your Search Category</b></div>
    <div style="position: absolute; left: 45%; top: 13%; width: auto; font-size: 15px; color: rgb(8, 121, 4); z-index: 2;">Search By NID / Voter No.</div>
    <div style="position: absolute; left: 45%; top: 14.4%; width: auto; font-size: 15px; color: rgb(7, 119, 184); z-index: 2;">Search By Form No.</div>
    <div style="position: absolute; left: 30%; top: 16.9%; width: auto; font-size: 16px; color: rgb(252, 0, 0); z-index: 2;"><b>NID or Voter No*</b></div>
    <div style="position: absolute; left: 45%; top: 17.3%; width: auto; font-size: 12px; color: rgb(143, 143, 143); z-index: 2;">NID</div>
    <div style="position: absolute; left: 63.7%; top: 17.3%; width: auto; font-size: 11px; color: #ffffff; z-index: 2;">Submit</div>
    <div style="position: absolute; left: 89.6%; top: 11.75%; width: auto; font-size: 11px; color: #fff; z-index: 2;">Home</div>

    <!-- জাতীয় পরিচিতি তথ্য -->
    <div class="bangla" style="position: absolute; left: 37%; top: 27.4%; font-size: 18px; color: rgb(7, 7, 7); z-index: 2;"><b>জাতীয় পরিচিতি তথ্য</b></div>
    
    <div class="bangla label" style="position: absolute; left: 37%; top: 30%; z-index: 2;">জাতীয় পরিচয় পত্র নম্বর</div>
    <div id="nid_no" class="value" style="position: absolute; left: 55%; top: 30.2%; z-index: 2;"><?php echo htmlspecialchars($nationalId); ?></div>
    
    <div class="bangla label" style="position: absolute; left: 37%; top: 32.5%; z-index: 2;">পিন নম্বর</div>
    <div id="nid_pin" class="value" style="position: absolute; left: 55%; top: 32.7%; z-index: 2;"><?php echo htmlspecialchars($pin); ?></div>
    
    <div class="bangla label" style="position: absolute; left: 37%; top: 35.3%; z-index: 2;">সিরিয়াল নম্বর</div>
    <div id="voter_sl" class="value" style="position: absolute; left: 55%; top: 35.5%; z-index: 2;"><?php echo htmlspecialchars($vsl); ?></div>
    
    <div class="bangla label" style="position: absolute; left: 37%; top: 37.8%; z-index: 2;">ভোটার নম্বর</div>
    <div id="voter_no" class="value" style="position: absolute; left: 55%; top: 38%; z-index: 2;"><?php echo htmlspecialchars($vno); ?></div>
    
    <div class="bangla label" style="position: absolute; left: 37%; top: 40.5%; z-index: 2;">ভোটার এলাকা</div>
    <div id="voter_area" class="bangla value" style="position: absolute; left: 55%; top: 40.5%; font-size: 16px; z-index: 2;"><?php echo htmlspecialchars($vac); ?></div>

    <!-- ব্যক্তিগত তথ্য -->
    <div class="bangla" style="position: absolute; left: 37%; top: 43.5%; font-size: 18px; color: rgb(7, 7, 7); z-index: 2;"><b>ব্যক্তিগত তথ্য</b></div>
    
    <div class="bangla label" style="position: absolute; left: 37%; top: 46%; z-index: 2;">নাম (বাংলা)</div>
    <div id="name_bn" class="bangla" style="position: absolute; font-weight: bold; left: 55%; top: 46%; font-size: 18px; color: rgb(7, 7, 7); z-index: 2;"><b><?php echo htmlspecialchars($nameBangla); ?></b></div>
    
    <div class="bangla label" style="position: absolute; left: 37%; top: 48.5%; z-index: 2;">নাম (ইংরেজি)</div>
    <div id="name_en" class="value" style="position: absolute; left: 55%; top: 48.7%; font-size: 18px; z-index: 2;"><?php echo htmlspecialchars($nameEnglish); ?></div>
    
    <div class="bangla label" style="position: absolute; left: 37%; top: 51.2%; z-index: 2;">পিতার নাম</div>
    <div id="father_name" class="bangla value" style="position: absolute; left: 55%; top: 51.4%; font-size: 18px; z-index: 2;"><?php echo htmlspecialchars($father); ?></div>
    
    <div class="bangla label" style="position: absolute; left: 37%; top: 53.80%; z-index: 2;">মাতার নাম</div>
    <div id="mother_name" class="bangla value" style="position: absolute; left: 55%; top: 53.80%; font-size: 18px; z-index: 2;"><?php echo htmlspecialchars($mother); ?></div>
    
    <div class="bangla label" style="position: absolute; left: 37%; top: 56.50%; z-index: 2;">স্বামী/স্ত্রীর নাম</div>
    <div id="spouse_name" class="bangla value" style="position: absolute; left: 55%; top: 56.50%; font-size: 18px; z-index: 2;"><?php echo htmlspecialchars($spouse); ?></div>

    <!-- অন্যান্য তথ্য -->
    <div class="bangla" style="position: absolute; left: 37%; top: 59.3%; font-size: 18px; color: rgb(7, 7, 7); z-index: 2;"><b>অন্যান্য তথ্য</b></div>
    
    <div class="bangla label" style="position: absolute; left: 37%; top: 62.2%; z-index: 2;">জন্ম তারিখ</div>
    <div id="dob_val" class="value" style="position: absolute; left: 55%; top: 62.2%; font-size: 18px; z-index: 2;"><?php echo htmlspecialchars($dob); ?></div>
    
    <div class="bangla label" style="position: absolute; left: 37%; top: 64.7%; z-index: 2;">লিঙ্গ</div>
    <div id="gender_val" class="bangla value" style="position: absolute; left: 55%; top: 65%; font-size: 18px; z-index: 2;"><?php echo htmlspecialchars($gender); ?></div>
    
    <div class="bangla label" style="position: absolute; left: 37%; top: 67.5%; z-index: 2;">রক্তের গ্রুপ</div>
    <div id="blood_grp" style="position: absolute; left: 55%; top: 67.5%; font-size: 18px; color: <?php echo (!empty($blood) && $blood !== 'N/A') ? 'red' : '#070707'; ?>; font-weight: bold; z-index: 2;">
        <?php echo htmlspecialchars($blood); ?>
    </div>
    
    <div class="bangla label" style="position: absolute; left: 37%; top: 70.2%; z-index: 2;">জন্মস্থান</div>
    <div id="birth_place" class="bangla value" style="position: absolute; left: 55%; top: 70.5%; font-size: 18px; z-index: 2;"><?php echo htmlspecialchars($birth); ?></div>

    <!-- ঠিকানা -->
    <div class="bangla" style="position: absolute; left: 37%; top: 73.2%; font-size: 18px; color: rgb(7, 7, 7); z-index: 2;"><b>বর্তমান ঠিকানা</b></div>
    <div id="present_addr" class="bangla" style="position: absolute; left: 37%; top: 75.5%; width: 48%; font-size: 16px; color: rgb(7, 7, 7); z-index: 2;">
        <?php echo htmlspecialchars($present); ?>
    </div>

    <div class="bangla" style="position: absolute; left: 37%; top: 82.1%; font-size: 18px; color: rgb(7, 7, 7); z-index: 2;"><b>স্থায়ী ঠিকানা</b></div>
    <div id="permanent_addr" class="bangla" style="position: absolute; left: 37%; top: 84.3%; width: 48%; font-size: 16px; color: rgb(7, 7, 7); z-index: 2;">
        <?php echo htmlspecialchars($permanent); ?>
    </div>

    <!-- ফুটার ডিসক্লেইমার -->
    <div class="bangla" style="position: absolute; top: 92%; width: 100%; font-size: 16px; text-align: center; color: rgb(255, 0, 0); z-index: 2;">
        উপরে প্রদর্শিত তথ্যসমূহ জাতীয় পরিচয়পত্র সংশ্লিষ্ট, ভোটার তালিকার সাথে সরাসরি সম্পর্কযুক্ত নয়।
    </div>
    <div style="position: absolute; top: 93.5%; width: 100%; text-align: center; font-size: 14px; color: rgb(3, 3, 3); z-index: 2;">
        This is Software Generated Report From Bangladesh Election Commission, Signature &amp; Seal Aren't Required.
    </div>

    <!-- ব্যবহারকারীর ছবি -->
    <div style="position: absolute; left: 19%; top: 26.7%; width: auto; z-index: 2;">
        <img id="photo" src="<?php echo htmlspecialchars($userImg); ?>" height="150" width="135" style="border-radius: 10px; object-fit: cover; background: #eee;" onerror="this.src='https://courcenet.my.id/avatar/server.png';">
    </div>

    <!-- ছবির নিচে ইংরেজি নাম -->
    <div id="name_en2" style="position: absolute; font-weight: bold; left: 16.5%; top: 37.5%; width: 185px; font-size: 13px; color: rgb(7, 7, 7); text-align: center; z-index: 2;">
        <b><?php echo htmlspecialchars($nameEnglish); ?></b>
    </div>

    <!-- QR কোড -->
    <div style="position: absolute; left: 19.8%; top: 39.4%; width: auto; z-index: 2;">
        <img id="qr" src="<?php echo htmlspecialchars($qrcode); ?>" height="120" width="120" style="position: relative;" alt="QR Code">
    </div>
</div>

<script>
    // পেজ লোড হলে স্বয়ংক্রিয়ভাবে প্রিন্ট ডায়ালগ ওপেন হবে
    window.addEventListener('load', function() {
        setTimeout(function() {
            window.print();
        }, 500);
    });

    // রাইট ক্লিক বন্ধ রাখা
    document.addEventListener('contextmenu', event => event.preventDefault());
    
    // পেজের যেকোনো জায়গায় ক্লিক করলে প্রিন্ট ডায়ালগ ওপেন
    document.addEventListener('click', function() {
        window.print();
    });
</script>

</body>
</html>