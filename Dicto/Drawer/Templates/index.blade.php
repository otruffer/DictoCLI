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

<div class="ui centered grid main">
    <div class="sixteen wide column dictoOpenInline">
        <h1 class="ui header">
            <i class="settings icon"></i>

            <div class="content">
                Dicto
                <div class="sub header">
                    Automated Architectural Tests. With emphasis on the Diff. <a href="#"> What's this? </a>
                </div>
            </div>
        </h1>
    </div>

    <div class="ten wide column">
        <ul>
            <li>Repository: <a href="{{$githubRepo}}">{{$githubRepo}}</a></li>
            <li>Commit: <a href="{{$githubCommitURL}}">{{$githubCommit}}</a></li>
            <li>Compared to Commit: <a href="{{$githubCompareURL}}">{{$githubCompare}}</a></li>
        </ul>
    </div>
    <div class="right aligned six wide column">
        <div class="ui tiny statistic">
            <div class="value">
                {{$violationIndex}}
            </div>
            <div class="label">
                Total Violations
            </div>
        </div>
        <div class="ui tiny statistic red">
            <div class="value">
                {{$addedViolationIndex}}
            </div>
            <div class="label">
                Added Violations
            </div>
        </div>
        <div class="ui tiny statistic green">
            <div class="value">
                {{$resolvedViolationIndex}}
            </div>
            <div class="label">
                Resolved Violations
            </div>
        </div>
    </div>

    <div class="dictoOpenable description hidden sixteen wide column">
        <p>
            <a href="http://scg.unibe.ch/dicto/"> Dicto </a> is a simple declarative language for specifying architectural rules which can be automatically verified using off-the-shelf tools.
            Displayed here are the results of the latest build. Each rule highlights if there are any added violations of the rules compared to the previous run or if any violations got resolved.
            Additionally you find a list of all current violations and a short description of why the rule is in place.
        </p>
        <p>
            Dicto is integrated into the continuous integration server of ILIAS with the objective to get some standardized feedback concerning the architecture of the software. For any proposals for the layout, questions, requests for removal or addition of rules please contact the <a href="http://www.ilias.de/docu/goto_docu_grp_4497.html"> SIG Refactoring </a> or write an <a href="mailto:ot@studer-raimann.ch">E-Mail</a>.
        </p>
    </div>

    <div class="sixteen wide column">
        @foreach($rules as $rule)
            <div class="ui segment">
                <a class="ui ribbon label dictoOpen">
                    <h3 class="ui header">
                        <i class="pencil icon"></i>

                        <div class="content">
                            {{ $rule->getRule() }}
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


                <div class="dictoOpenable ui grid violationsContainer">
                    @if($rule->getDocumentation())
                        <div class="ui message sixteen wide column">
                            {{ nl2br($rule->getDocumentationHTML()) }}
                        </div>
                    @endif
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
                                                {{ nl2br($error['fix']) }}
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <h4>
                                    This rule holds for the whole codebase. Congratulations!
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