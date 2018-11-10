<style>
	#s3-importer-progress {

	}

	#s3-importer-progress > button {
		margin-top: 20px;
	}

	.s3-importer-progress-container {
		position: relative;
		width: 100%;
		height: 32px;
		background: #AAA;
		border-radius: 16px;
		overflow: hidden;
	}

	#s3-importer-progress-bar {
		background-color: #3a84e6;
		height: 100%;
	width: {{$progress}}%;
	}

	.tool-disabled {
		padding: 10px 15px;
		border: 1px solid #df8403;
	}

	.force-cancel-help {
		margin-top: 20px;
	}

    .wp-cli-callout {
        padding: 10px;
        background-color: rgba(0,0,0,0.0625);
        margin-top: 20px;
        border-radius: 8px;
    }

    .wp-cli-callout > h3 {
        margin: 0; padding: 0;
        font-size: 14px;
    }

    #s3-timing-stats {
        display: none;
    }

    #s3-importer-status-text {
        position: absolute;
        left: 16px; top:0px; bottom: 0px; right: 16px;
        display: flex;
        align-items: center;
        color: white;
        font-weight: bold;
    }
</style>
<div class="settings-container">
	<header>
		<img src="{{ILAB_PUB_IMG_URL}}/icon-cloud.svg">
		<h1>{{$title}}</h1>
	</header>
	<div class="settings-body">
		<div id="s3-importer-instructions" {{($status=="running") ? 'style="display:none"':''}}>
			{{$instructions}}
            <div class="wp-cli-callout">
                <h3>Using WP-CLI</h3>
                <p>You can run this importer process from the command line using WP-CLI:</p>
                <code>
                    {{$commandLine}}
                </code>
            </div>
			<div style="margin-top: 2em;">
				<?php if($enabled): ?>
					<a href="#" class="ilab-ajax button button-primary">{{$commandTitle}}</a>
				<?php else: ?>
					<strong class="tool-disabled">Please <a href="admin.php?page=media-tools-top">{{$disabledText}}</a> before using this tool.</strong>
				<?php endif ?>
			</div>
		</div>
		<div id="s3-importer-progress" {{($status!="running") ? 'style="display:none"':''}}>
		    <div id="s3-importer-progress-text">
			<p id="s3-importer-cancelling-text" style="display:{{($shouldCancel) ? 'block':'none'}}">Cancelling ... This may take a minute ...</p>
		</div>
	    	<div class="s3-importer-progress-container">
			<div id="s3-importer-progress-bar"></div>
            <div id="s3-importer-status-text" style="visibility:{{($shouldCancel) ? 'hidden':'visible'}}">
                <div>Processing '<span id="s3-importer-current-file">{{$currentFile}}</span>' (<span id="s3-importer-current">{{$current}}</span> of <span id="s3-importer-total">{{$total}}</span>).  <span id="s3-timing-stats"><span id="s3-timing-ppm">{{number_format($postsPerMinute, 1)}}</span> posts per minute, ETA: <span id="s3-timing-eta">{{number_format($eta, 2)}}</span>.</span></div>
            </div>
		</div>
    		<button id="s3-importer-cancel-import" class="button button-warning" title="Cancel">{{$cancelCommandTitle}}</button>
        </div>
	</div>
</div>
<script>
    (function($){
        $(document).ready(function(){
            var importing={{($status == 'running') ? 'true' : 'false'}};

            $('#s3-importer-cancel-import').on('click', function(e){
                e.preventDefault();

                if (confirm("Are you sure you want to cancel?")) {
                    var data={
                        action: '{{$cancelAction}}'
                    };

                    $.post(ajaxurl,data,function(response){
                        $('#s3-importer-cancelling-text').css({'display':'block'});
                        $('#s3-importer-status-text').css({'visibility':'hidden'});
                        $('#s3-importer-cancel-import').attr('disabled', true);
                        console.log(response);
                    });
                }

                return false;
            });

            $('.ilab-ajax').on('click',function(e){
                e.preventDefault();

                if (importing)
                    return false;


                importing=true;

                var data={
                    action: '{{$startAction}}'
                };

                $.post(ajaxurl,data,function(response){
                    if (response.status == 'error') {
                        document.location.reload();
                    }

                    if (response.status == 'running') {
                        $('#s3-importer-cancel-import').attr('disabled', false);
                        $('#s3-importer-cancelling-text').css({'display':'none'});
                        $('#s3-importer-status-text').css({'visibility':'visible'});

                        $('#s3-importer-instructions').css({display: 'none'});
                        $('#s3-importer-progress').css({display: 'block'});
                    }
                });
                return false;
            });

            var checkStatus = function() {
                if (importing) {
                    var data={
                        action: '{{$progressAction}}'
                    };

                    $.post(ajaxurl,data,function(response){
                        if (response.shouldCancel) {
                            $('#s3-importer-cancelling-text').css({'display':'block'});
                            $('#s3-importer-status-text').css({'visibility':'hidden'});
                        } else {
                            $('#s3-importer-cancelling-text').css({'display':'none'});
                            $('#s3-importer-status-text').css({'visibility':'visible'});
                        }

                        if (response.status != 'running') {
                            importing = false;
                            $('#s3-importer-instructions').css({display: 'block'});
                            $('#s3-importer-progress').css({display: 'none'});
                        } else {
                            if (response.total > 0) {
                                var progress = (response.current / response.total) * 100;
                                $('#s3-importer-progress-bar').css({width: progress+'%'});
                            }

                            $('#s3-timing-stats').css({display: 'inline-block'});

                            $('#s3-importer-current').text(response.current);
                            $('#s3-importer-current-file').text(response.currentFile);
                            $('#s3-importer-total').text(response.total);
                            $('#s3-timing-ppm').text(parseFloat(response.postsPerMinute).toFixed(1));

                            var date = new Date();
                            date.setSeconds(date.getSeconds() + (parseFloat(response.eta) * 60.0));

                            $('#s3-timing-eta').text(date.toLocaleTimeString());
                        }
                    });
                }

                setTimeout(checkStatus, 3000);
            };

            setTimeout(checkStatus, 3000);
        });
    })(jQuery);
</script>