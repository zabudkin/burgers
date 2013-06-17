<?php

$f3=require('lib/base.php');
$f3->set("AUTOLOAD", "../lib");

class MyTestSqlMapper extends SqlMapper
{
  function __construct()
  {
    parent::__construct(new DB\SQL("sqlite:/tmp/test.sqlite"), "MyTest");
    $this->properties = array(
      "mybool" => array(
        "type" => "Boolean",
      ),
      "mycolor" => array(
        "type" => "Color",
      ),
      "myemail" => array(
        "type" => "Email",
      ),
      "myfloat" => array(
        "type" => "Float"
      ),
      "myint" => array(
        "type" => "Integer",
      ),
      "mynumeric" => array(
        "type" => "Numeric",
      ),
      "mytext" => array(
        "type" => "Text",
      ),
    );
  }
}

$test=new Test;
$instance=new MyTestSqlMapper;
$form=new Form($instance);

//
// Render the form
//
try {
  echo $form->render();
  echo "\n";
} catch (Exception $e) {
  echo $e->getTraceAsString();
}

//
// Validate the form
//
try {
  $test->expect(!$form->validate(array("mybool" => "Hello World!", "mycolor" => "#FFFFFF", "myemail" => "test@abc.de", "myfloat" => 1.0, "myint" => 1, "mynumeric" => 1, "mytext" => true)), "Errors with wrong boolean and wrong text. " . print_r($f3->get("form_errors"), true));
} catch (Exception $e) {
  $test->expect(false, "Errors with wrong boolean and wrong text. Exception: " . $e->getMessage());
}

$test->expect($form->validate(array("mybool" => true, "mycolor" => "#FFFFFF", "myemail" => "test@abc.de", "myfloat" => 1.0, "myint" => 1, "mynumeric" => 1, "mytext" => "Hello World!")), "No errors with valid data.");

//
// Save the form
// 
try {
  $form->save(array("mybool" => true, "mycolor" => "#FFFFFF", "myemail" => "test@abc.de", "myfloat" => 1.0, "myint" => 1, "mynumeric" => 1, "mytext" => "Hello World!"));
  $test->expect($instance->count("myint = 1") > 0, "Saving the form.");
  $instance->erase("myint = 1");
} catch (Exception $e) {
  $test->expect(false, "Saving the form. " . $e->getMessage());
}

// Display the results; not MVC but let's keep it simple
foreach ($test->results() as $result) {
  echo $result['text']."\t";
  if ($result['status'])
    echo 'Pass';
  else
    echo 'Fail ('.$result['source'].')';
  echo "\n";
}
