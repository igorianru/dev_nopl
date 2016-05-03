<?php

namespace App\Http\Controllers;

use App\Attachment;
use App\Document;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class MainController extends Controller
{
    /**
     * MainController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct();

        $this->document = new Document();
        $this->attachment = new Attachment();
        $this->request = $request->all();
        $this->requests = $request;
    }

    /**
     * вывод списка документов
     * @return mixed
     */
    public function index()
    {
        try {
            $Document = $this->document;

            $document = $Document->get();

            return view('client.main.index', [
                'document' => $document
            ]);
        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response($err->getMessage(), 500);
        }
    }
    
    /**
     * создание документа
     * @return mixed
     */
    public function create()
    {
        try {
            $Document = $this->document;

            $document = $Document->get();
            $timestamp = time();

            return view('client.main.create', [
                'document' => $document,
                'timestamp' => $timestamp,
                'document_id' => $timestamp
            ]);
        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response($err->getMessage(), 500);
        }
    }

    /**
     * создание документа create_save
     * @param null $r
     * @return mixed
     */
    public function create_save($r = null)
    {
        try {
            $request = $this->request;

            if(isset($request['text']))
            {
                $Document = $this->document;

                $Document->text = $request['text'];
                $Document->name = $request['name'];

                $Document->save();
                $document_id =  $Document->id;

                $File = $this->attachment;

                if(!isset($request['files']))
                {
                    $request['files'] = [];
                }

                foreach ($request['files'] as $key => $v)
                {
                    $file = $File->find($v);

                    if(!empty($file))
                    {
                        $file->active = 1;
                        $file->document_id = $document_id;
                        $file->order_file = $key;

                        $file->save();
                    }
                }

                if($r) {
                    return redirect('/document/edit/' . $document_id);
                } else {
                    return redirect('/document');
                }
            }
        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response($err->getMessage(), 500);
        }
    }

    /**
     * редактирование документа
     * @param null $id
     * @return mixed
     */
    public function edit($id = null)
    {
        try {
            if($id)
            {
                $Document = $this->document;
                $Attachment =  $this->attachment;

                $document = $Document->find($id);

                if(empty($document)) {
                    Session::flash('error', 'Ошибка получения');

                    return redirect()->back();
                }

                $files = $Attachment->where(['document_id' => $id])->orderBy('order_file', 'ASC')->get();

                $timestamp = time();

                return view('client.main.edit', [
                    'document' => $document,
                    'files' => $files,
                    'timestamp' => $timestamp,
                    'id' => $id
                ]);
            }
        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response($err->getMessage(), 500);
        }
    }

    /**
     *  редактирование документа edit_save
     * @param null $id
     * @param null $r
     * @return mixed
     */
    public function edit_save($id = null, $r = null)
    {
        try {
            $request = $this->request;
            
            if($id && isset($request['text']))
            {
                $Document = $this->document;
                $document = $Document->find($id);

                if(empty($document)) {
                    Session::flash('error', 'Ошибка получения');

                    return redirect()->back();
                }

                $document->text = $request['text'];
                $document->name = $request['name'];

                $document->save();

                $File = $this->attachment;

                if(!isset($request['files']))
                {
                    $request['files'] = [];
                }

                foreach ($request['files'] as $key => $v)
                {
                    $file = $File->find($v);

                    if(!empty($file))
                    {
                        $file->active = 1;
                        $file->document_id = $id;
                        $file->order_file = $key;

                        $file->save();
                    }
                }

                if($r) {
                    return redirect('/document/edit/' . $id);
                } else {
                    return redirect('/document');
                }
            }
        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response($err->getMessage(), 500);
        }
    }

    /**
     * загрузка файла
     * @param null $data
     * @return mixed
     */
    public function upload_file($data = null)
    {
        try {
            if(!$data) {
                $request = $this->requests;
                $filePath = public_path() . "/images/files/";

                if ($request->hasFile("Filedata"))
                {
                    $file = $request->file('Filedata');

                    $document_id = $request['document_id'];
                    $fileExt = strtolower($file->getClientOriginalExtension());
                    $orig_name = $file->getClientOriginalName();
                    $size = $file->getSize();
                    $fileName = str_random(10) . time() . str_random(5);

                    $file->move($filePath, $fileName .'.'. $fileExt);
                    chmod($filePath . $fileName .'.'. $fileExt, 0777);

                    $Attachment = $this->attachment;

                    $Attachment->size = $size;
                    $Attachment->name = $fileName .'.'. $fileExt;
                    $Attachment->orig_name = $orig_name;
                    $Attachment->type = $fileExt;
                    $Attachment->document_id = $document_id;

                    $Attachment->save();

                    $res['id'] = $Attachment->id;
                    $res['orig_name'] = $orig_name;
                    $res['name'] = $fileName .'.'. $fileExt;
                    $res['result'] = 'ok';
                } else {
                    $res['result'] = 'error';
                }
            }

            return $res;
        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response($err->getMessage(), 500);
        }
    }

    /**
     * удаление файла
     * @param null $id
     * @return mixed
     */
    function delete_file($id = null)
    {
        try {
            if(!$id)
            {
                $id = $this->requests['id'];
            }

            if($id)
            {
                $file = $this->attachment->find($this->requests['id']);

                if(!empty($file))
                {
                    $filePath = public_path() . "/images/files/" . $file->name;

                    if (File::exists($filePath)) {
                        $status = File::delete($filePath);

                        $res['status'] = $status;
                        $res['result'] = 'ok';
                    } else {
                        $res['text_error'] = 'file not found';
                        $res['result'] = 'error';
                    }

                    $file->delete();
                } else {
                    $res['result'] = 'error';
                    $res['text_error'] = 'error getting file';
                }
            } else {
                $res['result'] = 'error';
            }

            return json_encode($res);
        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response($err->getMessage(), 500);
        }
    }

    /**
     * удаление документа + файлы документа
     * @param $id
     */
    function delete($id)
    {
        try {
            $Attachment = $this->attachment;
            $Document = $this->document;
            $document = $Document->find($id);

            if(empty($document)) {
                Session::flash('error', 'Ошибка получения');

                return redirect()->back();
            }

            $files = $Attachment->where(['document_id' => $id])->get();

            foreach ($files as $v)
            {
                $this->delete_file($v->id);
            }

            $document->delete();

            return redirect('/document');
        } catch (\Exception $err) {
            Log::error($err->getMessage());
            return response($err->getMessage(), 500);
        }
    }
}