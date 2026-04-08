<!-- main-header opened -->
			<div class="main-header sticky side-header nav nav-item">
				<div class="container-fluid">
					<div class="main-header-left ">
						<div class="responsive-logo">
							<a href="{{ url('/' . $page='index') }}"><img src="{{URL::asset('assets/img/brand/logo.png')}}" class="logo-1" alt="logo"></a>
							<a href="{{ url('/' . $page='index') }}"><img src="{{URL::asset('assets/img/brand/logo-white.png')}}" class="dark-logo-1" alt="logo"></a>
							<a href="{{ url('/' . $page='index') }}"><img src="{{URL::asset('assets/img/brand/favicon.png')}}" class="logo-2" alt="logo"></a>
							<a href="{{ url('/' . $page='index') }}"><img src="{{URL::asset('assets/img/brand/favicon.png')}}" class="dark-logo-2" alt="logo"></a>

                         <h1 class="text-2xl font-bold text-indigo-600">OFMS</h1>
                        </div>
						<div class="app-sidebar__toggle" data-toggle="sidebar">
							<a class="open-toggle" href="#"><i class="header-icon fe fe-align-left" ></i></a>
							<a class="close-toggle" href="#"><i class="header-icons fe fe-x"></i></a>
						</div>
						<div class="main-header-center mr-3 d-sm-none d-md-none d-lg-block">
							{{-- <input class="form-control" placeholder="Search for anything..." type="search"> <button class="btn"><i class="fas fa-search d-none d-md-block"></i></button> --}}
						</div>
					</div>
					<div class="main-header-right">

						<div class="nav nav-item  navbar-nav-right ml-auto">
							<div class="nav-link" id="bs-example-navbar-collapse-1">
								<form class="navbar-form" role="search">
									<div class="input-group">
										<input type="text" class="form-control" placeholder="Search">
										<span class="input-group-btn">
											<button type="reset" class="btn btn-default">
												<i class="fas fa-times"></i>
											</button>
											<button type="submit" class="btn btn-default nav-link resp-btn">
												<svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
											</button>
										</span>
									</div>
								</form>
							</div>

							<div class="dropdown nav-item main-header-notification">
								<style>
									.notification-dropdown {
										min-width: 420px;
										max-width: 480px;
										border-radius: 12px;
										box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
										overflow: hidden;
										border: 1px solid #e8ecf1;
										animation: slideDown 0.25s ease;
										right: 0 !important;
										left: auto !important;
									}

									@keyframes slideDown {
										from {
											opacity: 0;
											transform: translateY(-8px);
										}
										to {
											opacity: 1;
											transform: translateY(0);
										}
									}

									.notification-dropdown-header {
										background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
										padding: 1.5rem;
										color: white;
										position: relative;
										overflow: hidden;
									}

									.notification-dropdown-header::before {
										content: '';
										position: absolute;
										top: 0;
										right: 0;
										width: 100px;
										height: 100px;
										background: rgba(255, 255, 255, 0.1);
										border-radius: 50%;
										transform: translate(30%, -30%);
									}

									.notification-dropdown-header-content {
										position: relative;
										z-index: 1;
									}

									.notification-dropdown-title {
										margin: 0;
										font-size: 1.15rem;
										font-weight: 700;
										letter-spacing: -0.5px;
									}

									.notification-dropdown-subtitle {
										opacity: 0.9;
										font-size: 0.85rem;
										margin-top: 0.5rem;
										font-weight: 400;
									}

									.notification-badge-new {
										display: inline-block;
										background: rgba(255, 255, 255, 0.3);
										color: white;
										padding: 0.35rem 0.75rem;
										border-radius: 20px;
										font-size: 0.75rem;
										font-weight: 600;
										margin-top: 0.5rem;
									}

									.notification-dropdown-list {
										max-height: 380px;
										overflow-y: auto;
										background: white;
									}

									.notification-dropdown-list::-webkit-scrollbar {
										width: 6px;
									}

									.notification-dropdown-list::-webkit-scrollbar-track {
										background: #f1f3f5;
									}

									.notification-dropdown-list::-webkit-scrollbar-thumb {
										background: #cbd5e1;
										border-radius: 3px;
									}

									.notification-dropdown-list::-webkit-scrollbar-thumb:hover {
										background: #94a3b8;
									}

									.notification-item {
										padding: 1.25rem;
										border-bottom: 1px solid #f1f3f5;
										transition: all 0.3s ease;
										display: flex;
										gap: 1rem;
										text-decoration: none;
										color: inherit;
										cursor: pointer;
										background: white;
									}

									.notification-item:hover {
										background: #f8fafc;
										border-left: 3px solid #667eea;
										padding-left: calc(1.25rem - 3px);
									}

									.notification-item-icon {
										flex-shrink: 0;
										width: 44px;
										height: 44px;
										background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
										border-radius: 10px;
										display: flex;
										align-items: center;
										justify-content: center;
										color: white;
										font-size: 1.1rem;
										box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
									}

									.notification-item-content {
										flex: 1;
										min-width: 0;
									}

									.notification-item-title {
										margin: 0 0 0.4rem 0;
										font-weight: 600;
										color: #2d3748;
										font-size: 0.95rem;
									}

									.notification-item-message {
										margin: 0 0 0.6rem 0;
										color: #64748b;
										font-size: 0.85rem;
										line-height: 1.5;
										word-break: break-word;
									}

									.notification-item-time {
										color: #a0aec0;
										font-size: 0.8rem;
										display: flex;
										align-items: center;
										gap: 0.35rem;
									}

									.notification-item-unread {
										flex-shrink: 0;
										width: 10px;
										height: 10px;
										background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
										border-radius: 50%;
										margin-top: 0.4rem;
										box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
									}

									.notification-empty {
										padding: 3rem 1.5rem;
										text-align: center;
										color: #cbd5e1;
										background: white;
									}

									.notification-empty-icon {
										font-size: 3rem;
										opacity: 0.25;
										display: block;
										margin-bottom: 1rem;
									}

									.notification-empty-text {
										margin: 0;
										font-size: 0.95rem;
										color: #64748b;
										font-weight: 500;
									}

									.notification-dropdown-footer {
										padding: 1rem;
										border-top: 1px solid #f1f3f5;
										background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
										text-align: center;
									}

									.notification-dropdown-footer a {
										color: #667eea;
										text-decoration: none;
										font-weight: 600;
										font-size: 0.9rem;
										display: inline-flex;
										align-items: center;
										gap: 0.5rem;
										transition: all 0.3s ease;
										padding: 0.5rem 1rem;
										border-radius: 6px;
									}

									.notification-dropdown-footer a:hover {
										background: rgba(102, 126, 234, 0.1);
										color: #764ba2;
										transform: translateY(-1px);
									}

									.show .notification-dropdown {
										animation: slideDown 0.25s ease;
									}
								</style>

								@php
									$unreadNotifications = auth()->user()->unreadNotifications;
									$unreadCount = $unreadNotifications->count();
								@endphp
								<a class="new nav-link" href="#" role="button" data-toggle="dropdown">
									<svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
									@if($unreadCount > 0)
										<span class="badge badge-danger badge-counter">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
									@endif
								</a>
								<div class="dropdown-menu dropdown-menu-right notification-dropdown">
									<!-- Notification Header -->
									<div class="notification-dropdown-header">
										<div class="notification-dropdown-header-content">
											<div class="d-flex justify-content-between align-items-start">
												<div>
													<h6 class="notification-dropdown-title">Notifications</h6>
													<small class="notification-dropdown-subtitle">You have {{ $unreadCount }} unread notification{{ $unreadCount != 1 ? 's' : '' }}</small>
												</div>
												@if($unreadCount > 0)
													<span class="notification-badge-new">{{ $unreadCount }} New</span>
												@endif
											</div>
										</div>
									</div>

									<!-- Notification List -->
									<div class="notification-dropdown-list">
										@forelse($unreadNotifications as $notification)
											@php
												$nd = $notification->data;
												$ndTitle   = $nd['title']   ?? ($nd['type'] === 'test_uploaded' ? 'Test Uploaded' : 'Notification');
												$ndMessage = $nd['message'] ?? 'New notification';
												$ndType    = $nd['type']    ?? 'info';
												$ndUrl     = $nd['url']     ?? null;
												$ndIconMap = ['success'=>'fe-check-circle','warning'=>'fe-alert-triangle','danger'=>'fe-x-circle','info'=>'fe-bell','test_uploaded'=>'fe-upload'];
												$ndColorMap= ['success'=>'#10b981','warning'=>'#f59e0b','danger'=>'#ef4444','info'=>'#667eea','test_uploaded'=>'#667eea'];
												$ndIcon    = $ndIconMap[$ndType]  ?? 'fe-bell';
												$ndColor   = $ndColorMap[$ndType] ?? '#667eea';
											@endphp
											<a class="notification-item text-decoration-none"
											   href="{{ $ndUrl ?? route('notifications.index') }}"
											   onclick="markNotificationAsRead('{{ $notification->id }}'); return true;">
												<!-- Icon -->
												<div class="notification-item-icon" style="background: linear-gradient(135deg, {{ $ndColor }} 0%, {{ $ndColor }}cc 100%);">
													<i class="fe {{ $ndIcon }}"></i>
												</div>

												<!-- Content -->
												<div class="notification-item-content">
													<h6 class="notification-item-title">{{ $ndTitle }}</h6>
													<p class="notification-item-message">{{ Str::limit($ndMessage, 80) }}</p>
													<small class="notification-item-time">
														<i class="fe fe-clock" style="font-size: 0.7rem;"></i>
														{{ $notification->created_at->diffForHumans() }}
													</small>
												</div>

												<!-- Unread Indicator -->
												@if(!$notification->read_at)
													<div class="notification-item-unread"></div>
												@endif
											</a>
										@empty
											<div class="notification-empty">
												<i class="fe fe-inbox notification-empty-icon"></i>
												<p class="notification-empty-text">No new notifications</p>
												<small style="color: #a0aec0;">Check back later for new updates</small>
											</div>
										@endforelse
									</div>

									<!-- Footer -->
									<div class="notification-dropdown-footer">
										<a href="{{ route('notifications.index') }}">
											<i class="fe fe-eye"></i> View All Notifications
										</a>
									</div>
								</div>
							</div>

							<script>
								function markNotificationAsRead(notificationId) {
									const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
									fetch(`/notifications/${notificationId}/mark-as-read`, {
										method: 'POST',
										headers: {
											'X-CSRF-TOKEN': csrfToken,
											'Content-Type': 'application/json',
											'Accept': 'application/json'
										}
									})
									.then(response => response.json())
									.then(data => {
										const badge = document.querySelector('.badge-counter');
										if (badge) {
											let count = parseInt(badge.textContent) || 0;
											count = Math.max(0, count - 1);
											if (count > 0) {
												badge.textContent = count > 99 ? '99+' : count;
											} else {
												badge.style.display = 'none';
											}
										}
									})
									.catch(error => console.error('Error marking notification as read:', error));
								}
							</script>


							<div class="nav-item full-screen fullscreen-button">
								<a class="new nav-link full-screen-link" href="#"><svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-maximize"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path></svg></a>
							</div>
							<div class="dropdown main-profile-menu nav nav-item nav-link">
								<a class="profile-user d-flex" href=""><img alt="" src="{{URL::asset('assets/img/faces/6.jpg')}}"></a>
								<div class="dropdown-menu">
									<div class="main-header-profile bg-primary p-3">
										<div class="d-flex wd-100p">
											<div class="main-img-user"><img alt="" src="{{URL::asset('assets/img/faces/6.jpg')}}" class=""></div>
											<div class="mr-3 my-auto">
												<h6>{{Auth::user()->name}}</h6><span>{{Auth::user()->email}}</span>
											</div>
										</div>
									</div>
									<a class="dropdown-item" href=""><i class="bx bx-user-circle"></i>Profile</a>
									<a class="dropdown-item" href=""><i class="bx bx-cog"></i> Edit Profile</a>
									<a class="dropdown-item" href=""><i class="bx bxs-inbox"></i>Inbox</a>
									<a class="dropdown-item" href=""><i class="bx bx-envelope"></i>Messages</a>
									<a class="dropdown-item" href=""><i class="bx bx-slider-alt"></i> Account Settings</a>
									<a class="dropdown-item" href="{{ route('logout') }}"
                                     onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i
                                     class="bx bx-log-out"></i>Log Out</a>
                                     <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                     @csrf
                                     </form>
								</div>
							</div>
							<div class="dropdown main-header-message right-toggle">
								<a class="nav-link pr-0" data-toggle="sidebar-left" data-target=".sidebar-left">
									<svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
<!-- /main-header -->
