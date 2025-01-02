<?php

namespace App\Services\ContactList;

use App\Models\Contact;
use App\Models\ContactList;
use App\Trait\EvolutionTrait;
use Exception;
use Illuminate\Support\Facades\Auth;

class ContactListService
{

    use EvolutionTrait;

    public function search()
    {
        try{
            $auth = Auth::user();

            $contactLists = ContactList::with('contacts', 'user')
                ->where('user_id', $auth->id)
                ->get();            

            return [
                'status' => true,
                'data' => $contactLists
            ];

        } catch(Exception $error){
            return [
                'status' => false,
                'message' => $error->getMessage(),
                'statusCode' => 400
            ];  
        }
    }

    public function import($request)
    {
        try {
            $auth = Auth::user();

            $validated = $request->validate([
                'file' => 'required|file|mimes:csv,txt',
                'description' => 'required|string|max:255'
            ]);

            $contactList = ContactList::create([
                'description' => $validated['description'],
                'user_id' => $auth->id
            ]);
    
            $file = $validated['file'];
            $path = $file->getRealPath();
    
            $firstLine = fgets(fopen($path, 'r'));
            $delimiter = strpos($firstLine, ';') !== false ? ';' : ',';

            $data = array_map(function ($line) use ($delimiter) {
                $line = mb_convert_encoding($line, 'UTF-8', 'auto');
                return str_getcsv($line, $delimiter);
            }, file($path));
            
            if (empty($data) || count($data) < 2) {
                throw new Exception('O arquivo CSV está vazio ou não possui dados suficientes.');
            }
    
            $header = array_map('trim', $data[0]);
    
            unset($data[0]);
    
            $contacts = [];
            foreach ($data as $row) {
                if (count($row) !== count($header)) {
                    continue;
                }

                $rowData = array_combine($header, $row);
                
                if (isset($rowData['Nome']) && isset($rowData['Telefones'])) {
                    $cleanPhone = preg_replace('/\D/', '', $rowData['Telefones']);
            
                    if (!empty($cleanPhone)) {
                        $contacts[] = [
                            'name' => trim($rowData['Nome']),
                            'phone' => $cleanPhone,
                            'contact_list_id' => $contactList->id
                        ];
                    }
                }
            }
    
            if (empty($contacts)) {
                throw new Exception('Nenhum contato válido encontrado no arquivo.');
            }
    
            Contact::insert($contacts);
    
            return [
                'status' => true,
                'message' => count($contacts) . ' contatos importados com sucesso.',
            ];
    
        } catch (Exception $error) {
            return [
                'status' => false,
                'message' => $error->getMessage(),
                'statusCode' => 400,
            ];
        }
    }

    public function delete($id)
    {
        try{
            
            $contactList = ContactList::find($id);

            $contactList->contacts()->delete();

            $ContactListdescription = $contactList->description;

            $contactList->delete();

            return [
                'status' => true,
                'data' => $ContactListdescription
            ];

        } catch(Exception $error){
            return [
                'status' => false,
                'message' => $error->getMessage(),
                'statusCode' => 400
            ];  
        }
    }
}
