<?php
#error_reporting(E_ALL);
echo'<pre><h2>snmp test</h2>';
#readmibs();

$ip = "";
$oid = "";
$community = "";
$lookup = "";
$checks = array(
    "snmpwalk" => array("func" => "snmpwalk", "checked" => "false", "name" => "SNMP Walk"),
    "snmpwalkoid" => array("func" => "snmpwalkoid", "checked" => "false", "name" => "SNMP Walk OID"),
    "snmprealwalk" => array("func" => "snmprealwalk", "checked" => "false", "name" => "SNMP Real Walk"),
    "snmp2_get" => array("func" => "snmp2_get", "checked" => "false", "name" => "SNMP2 Get"),
    "snmp2_real_walk" => array("func" => "snmp2_real_walk", "checked" => "false", "name" => "SNMP2 Real Walk"),
    "snmpget" => array("func" => "snmpget", "checked" => "false", "name" => "SNMP Get"),
);

if (isset($_GET['ip'])) {
    $ip = $_GET['ip'];
}
if (isset($_GET['oid'])) {
    $oid = $_GET['oid'];
}
if (isset($_GET['lookup'])) {
    $lookup = $_GET['lookup'];
    $checks[$lookup]['checked'] = "checked";
}
if (isset($_GET['community'])) {
    $community = $_GET['community'];
}

if (isset($_POST['submit'])) {
    if ($_POST['ip'] <> "") {
        $ip = $_POST['ip'];
    }
    if ($_POST['oid'] <> "") {
        $oid = $_POST['oid'];
    }
    if ($_POST['community'] <> "") {
        $community = $_POST['community'];
    }
    if ($_POST['lookup'] <> "") {
        $lookup = $_POST['lookup'];
        $checks[$lookup]['checked'] = "checked";
    } else {
        $lookup = "snmpwalk";
        $checks['snmpwalkoid']['checked'] = "checked";
    }
}

if (isset($_POST['submit']) or isset($_GET)) {
    echo'
    <form action="' . $_SERVER['PHP_SELF'] . '" method="post">
        IP address: <input type="text" name="ip" value="' . $ip . '" /> <br>
        Community name: <input type="text" name="community" value="' . $community . '"/> <br>
        Object ID: <input type="text" name="oid" value="' . $oid . '"/> <br>
        Lookup type: <br>';
    foreach ($checks as $check) {
        echo'<input type="radio" name="lookup" value="' . $check['func'] . '"' . ($check['checked'] == "checked" ? "checked=\"checked\"" : "") . '/> ' . $check['name'] . '<br>';
    }
    echo'<br /><input type="submit" name="submit" value="submit"> <input type="submit" name="reset" value="reset">
    </form>
    <a href="' . $_SERVER['PHP_SELF'] . '?ip=' . $ip . '&oid='.$oid.'&community='.$community . ($lookup <> "" ? '&lookup=' . $lookup : '' ) . '">Link to this page</a>
<h3>Results</h3>';
    echo'function: ' . $lookup . '(' . $ip . ',' . $community . ',' . ($oid == "" ? "null" : $oid) . ');<br>';

    $result = call_user_func($lookup, $ip, $community, $oid);

    var_dump($result);
} else {
    $checks['snmpwalk']['checked'] = "checked";
    echo'
    <form action="' . $_SERVER['PHP_SELF'] . '" method="post">
        IP address: <input type="text" name="ip" /> <br>
        Community name: <input type="text" name="community" value="public" /> <br>
        Object ID: <input type="text" name="oid" /> <br>
        Lookup type: <br>';
    foreach ($checks as $check) {
        echo'<input type="radio" name="lookup" value="' . $check['func'] . '"' . ($check['checked'] == "checked" ? "checked=\"checked\"" : "") . '/> ' . $check['name'] . '<br>';
    }
    echo'<br /><input type="submit" name="submit" value="submit"> <input type="submit" name="reset" value="reset">
    </form>
';
}




if (error_get_last() <> null) {
    echo'Last error: ';
    print_r(error_get_last());
}
echo'</pre>';

function readmibs() {

    $count = 0;
    $mib_path = "./mibs";
    $handle = opendir($mib_path);
    if (isset($handle)) {
       #    echo "Directory handle: $handle <br>";
       #    echo "Files: <br>";
        while (false !== ($file = readdir($handle))) {
            if ($file != '.') {
                if ($file != '..') {
                    #  echo "Read : ";
                    #  echo "$mib_path.$file";
                    if (snmp_read_mib($mib_path . '/' . $file)) {
                        #     echo " Succesful <br>";
                        $count++;
                    } else {
                        #    echo " Failed <br>";
                    }
                }
            }
        }
    }
    closedir($handle);
    echo $count . ' MIBs loaded';
}

?>
