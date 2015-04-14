<!DOCTYPE html>
<html>
<head>
    <title>DICTō</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.address/1.6/jquery.address.min.js"></script>
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
            <div class="detail">
                {{$violationIndex}}
            </div>
        </div>
        <div class="ui small popup">
            This is the total number of violations over all rules.
        </div>
        <div class="ui label @if($violationIndexDiff > 0) {{"red"}} @elseif($violationIndexDiff < 0) {{"green"}} @endif">
            @if($violationIndexDiff > 0) + @endif
            @if($violationIndexDiff == 0) +/- @endif
            {{$violationIndexDiff}}
            &nbsp;
            <i class="lightning icon"></i>
        </div>
    </div>
    <div class="ui small popup">
        How many architectural violations are resolved and how many added compared to the previous build.
    </div>
    <div class="ten wide column">
        @foreach($rules as $rule)
            <div class="ui segment">
                <a class="ui ribbon label dictoOpen">
                    <h3 class="ui header">
                        <i class="pencil icon"></i>

                        <div class="content">
                            {{ $rule->getRule() }}
                            <div class="ui small popup">
                                The currently existing number of violations for this rule.
                            </div>
                        </div>
                    </h3>
                </a>

                <div class="ui top right attached label @if(count($rule->getAddedViolations()) > 0) {{"red"}} @elseif(count($rule->getResolvedViolations())) {{"green"}} @endif dictoOpen pointerCursor">
                    <div class="ui small popup">
                        How many architectural violations are resolved and how many added compared to the previous build
                        regarding this rule.
                    </div>
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
                        {{ nl2br($rule->getDocumentationHTML()) }}
                    </div>
                @endif

                <div class="dictoOpenable ui grid violationsContainer">
                    <div class="four wide column">
                        <div class="ui vertical menu">
                            <a class="item active addedViolations">
                                Added Violations
                                <div class="ui label">{{count($rule->getAddedViolations())}}</div>
                            </a>
                            <a class="item resolvedViolations">
                                Resolved Violations
                                <div class="ui label">{{count($rule->getResolvedViolations())}}</div>
                            </a>
                            <a class="item allViolations">
                                All Violations
                                <div class="ui label">{{count($rule->getErrors())}}</div>
                            </a>

                            <div class="item">
                                <div class="ui transparent icon input">
                                    <input class="search" type="text" placeholder="Search...">
                                    <i class="search icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="twelve wide column">
                        <div class="adddedViolations">
                            @if($rule->getAddedViolations())
                                <h4>Newly Introduced Violations</h4>
                                <ul class="ui list">
                                    @foreach( $rule->getAddedViolations() as $violation)
                                        <li class="violation">{{{ $violation['details'] }}}
                                            <div class="fix">
                                                {{ nl2br($violation['fix']) }}
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <h4>
                                    No added violations
                                </h4>
                            @endif
                        </div>
                        <div class="resolvedViolations">
                            @if($rule->getResolvedViolations())
                                <h4>Resolved Violations</h4>
                                <ul class="ui list">
                                    @foreach( $rule->getResolvedViolations() as $violation)
                                        <li class="violation">{{{ $violation['details'] }}}
                                            <div class="fix">
                                                {{ nl2br($violation['fix']) }}
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <h4>
                                    No resolved violations.
                                </h4>
                            @endif
                        </div>
                        <div class="allViolations">
                            @if( count($rule->getErrors()) )
                                <h4>
                                    All Current Violations
                                </h4>
                                <ul class="ui list">
                                    @foreach( $rule->getErrors() as $error)
                                        <li class="violation">{{{ $error['details'] }}}
                                            <div class="fix">
                                                {{ nl2br($violation['fix']) }}
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <h4>
                                    This rule holds for the hole codebase. Congratulations!
                                </h4>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

</body>
</html>