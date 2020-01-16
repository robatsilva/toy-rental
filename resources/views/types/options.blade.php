@foreach($types as $type)
<option value='{{ $type->id }}'
>{{ $type->description }}</option>
@endforeach