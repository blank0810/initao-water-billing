<div id="globalPageLoader" class="fixed inset-0 z-50 pointer-events-none hidden" aria-hidden="true">
	<div class="absolute inset-0 bg-black/20 backdrop-blur-md transition-opacity duration-200"></div>

	<div class="flex items-center justify-center min-h-screen px-4 relative z-10">
		<div id="globalPageLoaderCard" class="pointer-events-auto bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-8 max-w-sm w-full text-center flex flex-col items-center gap-4">
			<!-- Water Wave Loader -->
			<div class="flex items-center justify-center">
				<div class="relative w-20 h-20">
					<svg class="w-full h-full" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
						<defs>
							<linearGradient id="waveGradient" x1="0%" y1="0%" x2="0%" y2="100%">
								<stop offset="0%" style="stop-color:#3b82f6;stop-opacity:0.8" />
								<stop offset="100%" style="stop-color:#1d4ed8;stop-opacity:1" />
							</linearGradient>
						</defs>
						<circle cx="50" cy="50" r="45" fill="none" stroke="#e5e7eb" stroke-width="3" class="dark:stroke-gray-700"/>
						<path id="wave1" d="M 0 50 Q 12.5 40, 25 50 T 50 50 T 75 50 T 100 50 L 100 100 L 0 100 Z" fill="url(#waveGradient)" opacity="0.6">
							<animateTransform attributeName="transform" type="translate" from="-25 0" to="0 0" dur="1.5s" repeatCount="indefinite"/>
						</path>
						<path id="wave2" d="M 0 50 Q 12.5 45, 25 50 T 50 50 T 75 50 T 100 50 L 100 100 L 0 100 Z" fill="url(#waveGradient)" opacity="0.4">
							<animateTransform attributeName="transform" type="translate" from="-25 0" to="0 0" dur="2s" repeatCount="indefinite"/>
						</path>
						<path id="wave3" d="M 0 50 Q 12.5 48, 25 50 T 50 50 T 75 50 T 100 50 L 100 100 L 0 100 Z" fill="url(#waveGradient)" opacity="0.3">
							<animateTransform attributeName="transform" type="translate" from="-25 0" to="0 0" dur="2.5s" repeatCount="indefinite"/>
						</path>
					</svg>
					<div class="absolute inset-0 flex items-center justify-center">
						<i class="fas fa-tint text-blue-500 text-2xl animate-pulse"></i>
					</div>
				</div>
			</div>

			<!-- Status text -->
			<div>
				<p id="globalLoaderMessage" class="text-sm text-gray-700 dark:text-gray-200">This will take a moment...</p>
				<p id="globalLoaderSub" class="text-xs text-gray-500 dark:text-gray-400 mt-1 hidden"></p>
			</div>

			<!-- Error / Retry -->
			<div id="globalLoaderActions" class="mt-2 hidden">
				<button id="globalLoaderRetry" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">Retry</button>
			</div>
		</div>
	</div>
</div>

@once
@push('scripts')
<script>
// Global Page Loader Script
(function(){
	const loaderRoot = document.getElementById('globalPageLoader');
	const messageEl = document.getElementById('globalLoaderMessage');
	const subEl = document.getElementById('globalLoaderSub');
	const actionsEl = document.getElementById('globalLoaderActions');
	const retryBtn = document.getElementById('globalLoaderRetry');

	if(!loaderRoot) return;

	let pendingRequests = 0;
	let visible = false;
	let showTimestamp = 0;
	let tWarning = null;
	let tError = null;
	let hideDelayTimer = null;

	const showDelay = 100; // small delay before showing to avoid flicker on very fast ops

	function clearTimers(){
		if(tWarning){ clearTimeout(tWarning); tWarning = null; }
		if(tError){ clearTimeout(tError); tError = null; }
		if(hideDelayTimer){ clearTimeout(hideDelayTimer); hideDelayTimer = null; }
	}

	function setMessage(text){
		if(messageEl) messageEl.textContent = text;
	}

	function enterErrorState(){
		if(!loaderRoot) return;
		// show error message and Retry
		setMessage('Something went wrong. Please try again.');
		if(subEl){ subEl.classList.add('hidden'); }
		if(actionsEl){ actionsEl.classList.remove('hidden'); }
	}

	function enterWarningState(){
		if(!loaderRoot) return;
		setMessage('This is taking longer than expected. Please wait...');
		if(subEl){ subEl.classList.add('hidden'); }
		if(actionsEl){ actionsEl.classList.add('hidden'); }
	}

	function resetState(){
		setMessage('This will take a moment...');
		if(subEl){ subEl.classList.add('hidden'); subEl.textContent = ''; }
		if(actionsEl){ actionsEl.classList.add('hidden'); }
	}

	function showImmediate(){
		if(!loaderRoot) return;
		loaderRoot.classList.remove('hidden');
		loaderRoot.style.pointerEvents = 'auto';
		visible = true;
	}

	function hideImmediate(){
		if(!loaderRoot) return;
		loaderRoot.classList.add('hidden');
		loaderRoot.style.pointerEvents = 'none';
		visible = false;
	}

	function showGlobalLoader(){
		// if already visible, do nothing
		if(visible) return;
		// small delay to avoid flashing for very fast requests
		if(hideDelayTimer){ clearTimeout(hideDelayTimer); hideDelayTimer = null; }
		setTimeout(()=>{
			showTimestamp = Date.now();
			resetState();
			showImmediate();

			// schedule warning and error messages
			tWarning = setTimeout(()=>{
				enterWarningState();
			}, 5000);

			tError = setTimeout(()=>{
				enterErrorState();
			}, 8000);
		}, showDelay);
	}

	function hideGlobalLoader(){
		clearTimers();
		// avoid immediate hide if shown for a very short amount; keep at least 300ms visible to avoid jank
		const elapsed = Date.now() - showTimestamp;
		const minVisible = 300;
		if(elapsed < minVisible){
			hideDelayTimer = setTimeout(()=>{ hideImmediate(); hideDelayTimer = null; }, minVisible - elapsed);
		} else {
			hideImmediate();
		}
	}

	// Expose globally for manual control
	window.showGlobalLoader = showGlobalLoader;
	window.hideGlobalLoader = hideGlobalLoader;

	// Retry button: by default will reload the page which typically retries the failing request
	retryBtn && retryBtn.addEventListener('click', function(){
		// hide loader then reload
		hideGlobalLoader();
		setTimeout(()=>{ location.reload(); }, 200);
	});

	// Network instrumentation
	(function instrumentFetch(){
		if(!window.fetch) return;
		const origFetch = window.fetch.bind(window);
		window.fetch = function(){
			pendingRequests++;
			if(pendingRequests === 1) showGlobalLoader();
			return origFetch.apply(this, arguments).finally(()=>{
				pendingRequests = Math.max(0, pendingRequests-1);
				if(pendingRequests === 0) hideGlobalLoader();
			});
		};
	})();

	(function instrumentXhr(){
		const origXHR = window.XMLHttpRequest;
		function X(){
			const xhr = new origXHR();
			const origOpen = xhr.open;
			xhr.open = function(){
				// attach listeners when send is called
				origOpen.apply(xhr, arguments);
			};
			const origSend = xhr.send;
			xhr.send = function(){
				try{
					pendingRequests++;
					if(pendingRequests === 1) showGlobalLoader();
				} catch(e){}
				const onDone = function(){
					try{ pendingRequests = Math.max(0, pendingRequests-1); if(pendingRequests === 0) hideGlobalLoader(); } catch(e){}
				};
				xhr.addEventListener('load', onDone);
				xhr.addEventListener('error', onDone);
				xhr.addEventListener('abort', onDone);
				return origSend.apply(xhr, arguments);
			};
			return xhr;
		}
		try{ window.XMLHttpRequest = X; } catch(e){}
	})();

	// axios support (if used)
	(function instrumentAxios(){
		try{
			const axios = window.axios || (window.axios && window.axios.create && window.axios);
			if(!axios) return;
			if(axios.interceptors && axios.interceptors.request){
				axios.interceptors.request.use(function(config){
					pendingRequests++;
					if(pendingRequests === 1) showGlobalLoader();
					return config;
				}, function(err){ return Promise.reject(err); });
				axios.interceptors.response.use(function(resp){ pendingRequests = Math.max(0, pendingRequests-1); if(pendingRequests === 0) hideGlobalLoader(); return resp; }, function(err){ pendingRequests = Math.max(0, pendingRequests-1); if(pendingRequests === 0) hideGlobalLoader(); return Promise.reject(err); });
			}
		} catch(e){}
	})();

	// Navigation handlers: show on beforeunload (full page navigations) and same-origin link clicks
	window.addEventListener('beforeunload', function(){ showGlobalLoader(); });

	document.addEventListener('click', function(e){
		try{
			const a = e.target.closest && e.target.closest('a');
			if(!a) return;
			if(a.target && a.target === '_blank') return; // external/tab
			const href = a.getAttribute('href');
			if(!href) return;
			// ignore links that are anchors or external
			if(href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
			const url = new URL(href, window.location.href);
			if(url.origin !== window.location.origin) return; // external
			// same-origin navigation -> show loader
			showGlobalLoader();
		} catch(e){}
	}, { capture: true });

	// If there are already pending requests at startup, show loader
	if(window.__INITIAL_PENDING_REQUESTS__ && window.__INITIAL_PENDING_REQUESTS__ > 0){
		pendingRequests = window.__INITIAL_PENDING_REQUESTS__;
		showGlobalLoader();
	}

})();
</script>
@endpush
@endonce

