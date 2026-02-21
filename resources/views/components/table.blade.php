{{-- Table Component --}}
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    @foreach($columns as $column)
                        <td>
                            @if(is_array($row) && isset($row[$column]))
                                {{ $row[$column] }}
                            @elseif(is_object($row) && isset($row->$column))
                                {{ $row->$column }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}" class="text-center text-muted" style="padding: 30px;">
                        <i class="fas fa-inbox" style="font-size: 40px; opacity: 0.3;"></i>
                        <p style="margin-top: 10px;">لا توجد بيانات للعرض</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($pagination ?? false)
    <div class="d-flex justify-content-center">
        {{ $pagination->links() }}
    </div>
@endif
