@extends('client.layouts.default')
@section('title',"Дедактирование документа")
@section('header', '
<script src="/js/upl_mul/jquery.uploadifive.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    ')


@section('content')
    <script>
        $(function() {
            $( "#files" ).sortable();
            $( "#files" ).disableSelection();
        });
    </script>

    <form method="post">
        <ol class="breadcrumb">
            <li><a href="/">Главная</a></li>
            <li class="active">Редактирование документа</li>
        </ol>
        <div class="row">
            <div class="col-md-8">
                <input name="name" class="form-control" value="{{ $document->name }}"/>
                <textarea name="text" rows="12" class="form-control">{{ $document->text }}</textarea>
            </div>
            <div class="col-md-4">
                <input id="file_upload" name="file_upload" type="file" multiple="multiple">

                <span id="status"></span>
                <ul id="files">
                    @foreach($files as $val)
                        <li class="ui-state-default file-{{ $val->id }}">
                            <a target="_blank" href="/images/files/{{ $val->name }}">
                                <span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                {{ $val->orig_name }}
                            </a>
                            <a class="btn btn-danger closes" onclick="closes({{ $val->id }})" data-id="{{ $val->id }}">×</a>
                            <input type="hidden" name="files[]" value="{{ $val->id }}"/>
                        </li>
                    @endforeach

                </ul>

                    <div id="queue" class="alert queue"></div>
                    <div class="response_suss" id="response_suss">

                    </div>




                <script type="text/javascript">
                    $(function() {
                        $('#file_upload').uploadifive({
                            'formData'       : {
                                'timestamp'  : '{{ $timestamp }}',
                                'document_id': '{{ $id }}',
                                '_token'     : '{{ csrf_token() }}'
                            },
                            'auto'             : true,
                            'debug'            : true,
                            'queueID'          : 'queue',
                            'buttonText'       : 'Прикрепить файл',
                            'buttonClass'      : 'btn btn-primary',
                            'width'            : 350,
                            'height'           : 35,
                            'lineHeight'       :  '20px',
                            'uploadScript'     : '/document/upload_file/',
                            'onProgress'       : 'total',
                            'fileSizeLimit'    : '5024KB',
                            'onUploadComplete' : function(file, data)
                            {
                                var ds = JSON.parse(data);

                                $('#files').append('<li class="ui-state-default file-'+ds['id']+'">' +
                                        '<a target="_blank" href="/images/files/' + ds['name'] + '">' +
                                        '<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' +
                                        ds['orig_name']+
                                        '</a>' +
                                        '<a class="btn btn-danger closes" onclick="closes('+ds['id']+')" data-id="'+ds['id']+'">×</a>' +
                                        '<input type="hidden" name="files[]" value="'+ds['id']+'"/>' +
                                        '</li>');

                                $("#vt_a").show(100);
                            }
                        });
                    });

                    function closes(id) {
                        $.ajax({
                            type: "post",
                            url: '/document/delete_file/',
                            data: {
                                '_token' : '<?= csrf_token() ?>',
                                'id' : id
                            },
                            cache: false,
                            dataType: "JSON",
                            success: function (data) {
                                if (data['result'] == 'ok') {
                                    $('.file-' + id).remove();
                                } else {
                                    // error
                                }
                            }
                        });
                    }
                </script>
            </div>
        </div>



        <div class="row">
            <div class="col-md-8">
                <br >
                <button type="submit" class="btn btn-success">Сохранить</button>
                <button type="submit" class="btn btn-primary" formaction="/document/edit/{{ $id }}/1">Применить</button>
            </div>
        </div>
    </form>
@stop