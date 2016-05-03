@extends('client.layouts.default')
@section('title',"Главная")



@section('content')

    <div class="panel panel-default">
        <!-- Default panel contents -->
        <div class="panel-heading">Все документы</div>

        <!-- Table -->
        <table class="table">
            <tr>
                <th>#</th>
                <th>Название</th>
                <th>Описание</th>
                <th>Действие</th>
            </tr>
            @foreach($document as $val)
                <tr>
                    <th></th>
                    <th>
                        <a href="/document/edit/{{ $val->id }}">{{ $val->name }}</a>
                    </th>
                    <th>{!! mb_substr($val['text'], 0, 100, 'UTF-8') !!}</th>
                    <th>
                        <div class="text-right">
                            <a href="/document/edit/{{ $val->id }}" class="btn btn-primary">
                                <i class="glyphicon glyphicon-pencil"></i>
                            </a>
                            <a href="/document/delete/{{ $val->id }}" class="btn btn-danger">
                                <i class="glyphicon glyphicon-remove"></i>
                            </a>
                        </div>
                    </th>
                </tr>
            @endforeach
        </table>
    </div>

@stop