<!DOCTYPE html>
<html>
<head>
    <title>DICTō</title>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery.address/1.6/jquery.address.min.js"></script>
    <link rel="stylesheet" type="text/css" href="Public/semantic.min.css">
    <link rel="stylesheet" type="text/css" href="Public/dicto.css">
    <script src="Public/semantic.min.js"></script>
    <script src="Public/dicto.js"></script>
</head>
<body>

<div class="ui centered grid">
    <div class="eight wide column">
        <h1 class="ui header">
            <i class="settings icon"></i>

            <div class="content">
                Dicto
                <div class="sub header">
                    Automated Architecutre Tests. With emphasis on the Diff.
                </div>
            </div>
        </h1>
    </div>
    <div class="right aligned two wide column">
        <div class="ui label">
            Index
            <div class="detail"
                 data-title="Violation Index"
                 data-content="This is the total number of violations over all rules.">
                {{$violationIndex}}
            </div>
        </div>
        <div class="ui label @if($violationIndexDiff > 0) {{"red"}} @elseif($violationIndexDiff < 0) {{"green"}} @endif"
             data-title="Violation Diff"
             data-content="How many architectural violations are resolved and how many added compared to the previous build.">
            @if($violationIndexDiff > 0) + @endif
            @if($violationIndexDiff == 0) +/- @endif
            {{$violationIndexDiff}}
            &nbsp;
            <i class="lightning icon"></i>
        </div>
    </div>
    <div class="ten wide column">
        @foreach($rules as $rule)
            <div class="ui segment">
                <a class="ui ribbon label dictoOpen">
                    <h3 class="ui header">
                        <i class="pencil icon"></i>

                        <div class="content">
                            {{ $rule->getRule() }}
                            <div class="ui label"
                                 data-title="Violation Index"
                                 data-content="The currently existing number of violations for this rule.">
                                {{ count($rule->getErrors()) }}
                            </div>
                        </div>
                    </h3>
                </a>

                <div class="ui top right attached label @if(count($rule->getAddedViolations()) > 0) {{"red"}} @elseif(count($rule->getResolvedViolations())) {{"green"}} @endif dictoOpen pointerCursor"
                     data-title="Violation Diff"
                     data-content="How many architectural violations are resolved and how many added compared to the previous build regarding this rule.">
                    @if(count($rule->getAddedViolations()))
                        + {{ count($rule->getAddedViolations()) }}
                        &nbsp;
                    @endif
                    @if(count($rule->getResolvedViolations()))
                        - {{ count($rule->getResolvedViolations()) }}
                        &nbsp;
                    @endif
                    @if( !(count($rule->getResolvedViolations()) || count($rule->getAddedViolations())) )
                        +/- 0
                        &nbsp;
                    @endif
                    <i class="lightning icon"></i>
                </div>
                @if($rule->getDocumentation())
                    <div class="dictoOpenable ui message">
                        {{ nl2br($rule->getDocumentation()) }}
                    </div>
                @endif
                <div class="dictoOpenable">
                    @if($rule->getAddedViolations())
                        <h4>Newly Introduced Violations</h4>
                        <ul class="ui list">
                            @foreach( $rule->getAddedViolations() as $violation)
                                <li>{{{ $violation['cause'] }}}</li>
                            @endforeach
                        </ul>
                    @endif

                    @if($rule->getResolvedViolations())
                        <h4>Resolved Violations</h4>
                        <ul class="ui list">
                            @foreach( $rule->getResolvedViolations() as $violation)
                                <li>{{{ $violation['cause'] }}}</li>
                            @endforeach
                        </ul>
                    @endif

                    @if( count($rule->getErrors()) )
                        <h4 class="dictoOpen">
                            <i class="dropdown icon"></i>
                            All Current Violations
                        </h4>
                        <ul class="ui list dictoOpenable">
                            @foreach( $rule->getErrors() as $error)
                                <li>{{{ $error['cause'] }}}</li>
                            @endforeach
                        </ul>
                    @else
                        <h4>
                            This rule holds for the hole codebase. Congratulations!
                        </h4>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

</body>
</html>