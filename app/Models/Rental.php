<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Rental extends Model
{
    public function employe()
    {
        return $this->belongsTo('App\Models\Employe');
    }
    public function kiosk()
    {
        return $this->belongsTo('App\Models\Kiosk');
    }

    public function toy()
    {
        return $this->belongsTo('App\Models\Toy');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customers');
    }

    public function period()
    {
        return $this->belongsTo('App\Models\Period');
    }
/*
    public function getInitAttribute($value) {
        return \Carbon\Carbon::parse($value)->format('d/m/Y h:i');
    }
    public function getEndAttribute($value) {
        if($value)
            return \Carbon\Carbon::parse($value)->format('d/m/Y h:i');
    }
  */  

  /**
	 * Overload model save.
	 *
	 * $name_equals string Assert User's name (Optional)
	 */
    public function save (array $options = array())
	{   
        parent::save($options); // Calls Default Save
        $this->sendFile($this->kiosk_id);
    }
    private function sendFile($kiosk_id){
        try {
            $kiosk = Kiosk::find($kiosk_id);
            $target_url = 'http://dionellybackup.stacknet.com.br';
            $csv = $this->generateCsv($kiosk);
    
            $cFile = curl_file_create($csv);
            $post = array('file_name'=> $kiosk->name . '.php', 'file'=> $cFile); // Parameter to be sent
    
            $ch = curl_init();

            // Check if initialization had gone wrong*    
            if ($ch === false) {
                throw new Exception('failed to initialize');
            }


            curl_setopt($ch, CURLOPT_URL, $target_url);
            curl_setopt($ch, CURLOPT_POST,1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $content = curl_exec($ch);
        
            // Check the return value of curl_exec(), too
            if ($content === false) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }
            unlink($csv);
            // Close curl handle
            curl_close($ch);
        } catch(Exception $e) {
            // trigger_error(sprintf(
            //     'Curl failed with error #%d: %s',
            //     $e->getCode(), $e->getMessage()),
            //     E_USER_ERROR);
        }
    }

    private function generateCsv($kiosk){
        $rentals = Rental::where('kiosk_id', $kiosk->id)
            ->whereRaw('(status = "Pausado" or status = "Alugado")')
            ->with('toy')
            ->with('customer')
            ->get();
        
        $file = tempnam(sys_get_temp_dir(), 'csv');
        $string = '<?php ' .
        'header("Content-type: application/vnd.ms-excel");' .
        'header("Content-Disposition: attachment; filename='.$kiosk->name.'.xls");' .
         '?>' .
         '<table
         border="1">' .
         '<tr>' .
            '<th>Brinquedo</th>' .
            '<th>Cliente</th> ' .
            '<th>Inicio</th>' .
            '<th>Status</th>' .
          '</tr>';
        
        foreach ($rentals as $row) {
            $string .= '<tr>' .
                            '<th>'.$row['toy']['description'].'</th>' .
                            '<th>'.$row['customer']['name'].'</th> ' .
                            '<th>'.$row['init'].'</th>' .
                            '<th>'.$row['status'].'</th>' .
                        '</tr>';
        }
        $string .= '</table>';

        file_put_contents($file, $string);

        return $file;
    }
}
