<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="/images/logo.svg" class="logo" alt="COACHTECH">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
