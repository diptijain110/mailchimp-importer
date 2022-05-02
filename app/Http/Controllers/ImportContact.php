<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Newsletter;


class ImportContact extends Controller
{  
    private $rows = [];
   
    public $listId = '4a864cc5e1';

 
    public function import(Request $request)
    {

        $path = $request->file('file')->getRealPath();
        $records = array_map('str_getcsv', file($path));

        if (! count($records) > 0) {
           return 'Error...';
        }

        // Get field names from header column
        $fields = array_map('strtolower', $records[0]);

        // Remove the header column
        array_shift($records);

        foreach ($records as $record) {
            if (count($fields) != count($record)) {
                return 'csv_upload_invalid_data';
            }

            // Decode unwanted html entities
            $record =  array_map("html_entity_decode", $record);

            // Set the field name as key
            $record = array_combine($fields, $record);

            // Get the clean data
            $this->rows[] = $this->clear_encoding_str($record);
        }

       

        if($request->input('type') =='import')
        {

         foreach ($this->rows as $data) {

        Newsletter::subscribe( $data['email address'],['FNAME'=>$data['first name'], 'LNAME'=>$data['last name']]);

        } 
       }
       if($request->input('type') =='update')
       {
        

        foreach ($this->rows as $data) {
            Newsletter::subscribeOrUpdate( $data['email address'],['FNAME'=>$data['first name'], 'LNAME'=>$data['last name'],'ADDRESS'=>$data['streetaddress'].','. $data['city'].','. $data['state'].','. $data['zipcode'].','. $data['countryfull'] ,'PHONE'=>$data['phone'],'BIRTHDAY'=>$data['birthday'],'FIELD1'=>$data["custom field 1"],'FIELD2'=>$data["custom field 2"],'TITLE'=>$data["title"]]);
            Newsletter::addTags([$data["tags"]], $data['email address']);

       } 
      }

        return redirect()->back()->with('success','Contacts Added');
    }
    
    private function clear_encoding_str($value)
    {
        if (is_array($value)) {
            $clean = [];
            foreach ($value as $key => $val) {
                $clean[$key] = mb_convert_encoding($val, 'UTF-8', 'UTF-8');
            }
            return $clean;
        }
        return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
    }

}

 ?>