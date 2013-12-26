<?PHP
require_once("AbstractModel.php");
require_once("Contact.php");
error_reporting(E_ERROR | E_WARNING);

$contact = new Contact();
$contact->load(1);



print_r($contact->getData()); 

// Should print:
// id => 1,
// name => John Doe
// email => john@doe.com
echo '<br/><br/>';
echo $contact->getData('name');
//Should print:
// John Doe
$contact->setData('name', 'John Walker')->save(); //Should run an UPDATE query
echo '<br/><br/>';
print_r($contact->load(1)->getData());
//Should print
// id => 1,
// name => John Walker
// email => john@doe.com
$contact->setData(array(
		"id" => 1,
		"name" => "John Doe the 2nd",
		"email" => "john@doe2.com"
))->save();
echo '<br/><br/>';
print_r($contact->load(1)->getData());
//Should print
// id => 1,
// name => John Doe the 2nd
// email => john@doe2.com
$newContact = new Contact();
$newContact->setData(array(
		"name" => "Gilbert Barber",
		"email" => "gilbTheparrot@gmail.com"
));
$newContact->save(); //Should run an INSERT query as there is no predefined id
print_r($newContact->getData());
//Should print
// id => ? some auto increment number,
// name => Gilbert Barber
// email => gilbTheparrot@gmail.com

$newContact->delete(); //Should delete him Gilbert Barber from the database

// Added this line to reset row for testing.
$contact->setData(array(
		"id" => 1,
		"name" => "John Doe",
		"email" => "john@doe.com"
))->save(); 
