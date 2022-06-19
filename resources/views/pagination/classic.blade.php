@if ($paginator->hasPages())
    <div class="pagination">

        @if ($paginator->onFirstPage())
            <div class="pagination__item disabled">
                @if($paginator->onFirstPage() != $paginator->currentPage())
                    @include('svg.left-arrow')
                @endif
            </div>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pagination__item">
                @include('svg.left-arrow')
            </a>
        @endif


        <div class="pagination__item" style="font-size:22px">
            {{$paginator->currentPage()}}
        </div>

        @foreach ($elements as $element)

            {{--            @if (is_string($element))--}}
            {{--                <li class="disabled"><span>{{ $element }}</span></li>--}}
            {{--            @endif--}}



            {{--            @if (is_array($element))--}}
            {{--                @foreach ($element as $page => $url)--}}
            {{--                    @if ($page == $paginator->currentPage())--}}
            {{--                        <a class="pagination__item active my-active"><span>{{ $page }}</span></a>--}}
            {{--                    @else--}}
            {{--                        <a href="{{ $url }}" class="pagination__item">{{ $page }}</a>--}}
            {{--                    @endif--}}
            {{--                @endforeach--}}
            {{--            @endif--}}
        @endforeach



        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pagination__item">
                @include('svg.right-arrow')
            </a>
        @else
            <div class="pagination__item disabled">
                @if($paginator->currentPage() != $paginator->lastPage())
                    @include('svg.right-arrow')
                @endif
            </div>
        @endif
    </div>
@endif
