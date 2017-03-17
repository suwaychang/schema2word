<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Schema 產 Word</title>

        <!-- Fonts -->
        <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet" type="text/css">
    </head>
    <body>
        <div class="content col-md-6 col-md-offset-3">
        <div class="checkbox">
            <label>
                <input type="checkbox" id="allchecked"> 全選
            </label>
            <a class="btn btn-primary put"><i class="glyphicon glyphicon-save"></i> 輸出word</a>
        </div>
        <form id="form" action="{{ asset('put') }}" method="post">
        @foreach($table as $index => $item)
            <div class="checkbox">
                  <label>
                    <input type="checkbox" id="blankCheckbox" value="{{ $index }}" name="table[]" @if(!empty($select_table) && in_array($index, $select_table)) checked @endif>
                    {{ $index . ' (' . $item . ')' }}
                  </label>
            </div>
            @endforeach
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </form>
        </div>
    </body>
</html>
<script type="text/javascript" src="{{ asset('js/jquery-1.11.2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
<script type="text/javascript">
    $('#allchecked').click(function(event) {
        if($(this).is(':checked')){
            $('[name*="table"]').each(function(index, el) {
                $(this).prop('checked',true);
            });
        }else{
            $('[name*="table"]').each(function(index, el) {
                $(this).prop('checked',false);
            });
        }
    });

    $('a.put').click(function (argument) {
        var table = $('[name*="table"]:checked');
        if(table.size() == 0){
            alert('請選擇資料表');
        }else{
            $('#form').submit();
        }
    });
</script>
