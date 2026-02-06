<nav class="sidebar sidebar-offcanvas" id="sidebar">
	<ul class="nav">
		<li class="nav-item nav-profile">
			<a href="#" class="nav-link">
				<div class="profile-image">
					<img class="img-xs rounded-circle"
						src="<?= base_url('upload/' . $this->session->userdata('photo')); ?>" alt="profile image">
					<div class="dot-indicator bg-success"></div>
				</div>
				<div class="text-wrapper">
					<p class="profile-name"><?= $this->session->userdata('fullName'); ?></p>
					<p class="designation"><?= $this->session->userdata('privilageName'); ?></p>
				</div>
			</a>
		</li>
		<li class="nav-item nav-category">Main Menu</li>

		<li class="nav-item">
			<a class="nav-link" href="<?= site_url('Dashboard') ?>">
				<i class="menu-icon typcn typcn-document-text"></i>
				<span class="menu-title">Dashboard</span>
			</a>
		</li>
		<?php if ($this->session->userdata('privilege') == '1' || $this->session->userdata('privilege') == '2' || $this->session->userdata('privilege') == '0' || $this->session->userdata('privilege') == '9') { ?>
			<li class="nav-item">
				<a class="nav-link" data-toggle="collapse" href="#dashboard_cso" aria-expanded="false"
					aria-controls="dashboard_cso">
					<i class="menu-icon typcn typcn-coffee"></i>
					<span class="menu-title">CSO</span>
					<i class="menu-arrow"></i>
				</a>
				<div class="collapse" id="dashboard_cso">
					<ul class="nav flex-column sub-menu">
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('DashboardCso'); ?>">Dashboard CSO</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('Reports/counter_summary'); ?>">Counter Summary</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('Reports/cso_performance'); ?>">CSO Performance</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('Reports/cso_purchase'); ?>">CSO Purchase</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('Reports/cso_satisfaction'); ?>">CSO Rating
								Satisfaction</a>
						</li>
					</ul>
				</div>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="<?= site_url('DashboardHakbesik') ?>">
					<i class="menu-icon typcn typcn-document-text"></i>
					<span class="menu-title">Dashboard Hakbesik</span>
				</a>
			</li>
		<?php } ?>

		<!-- Setting dan Manage User -->
		<!-- manager -->

		<!-- akhir manager -->

		<!-- awal asisten manager -->
		<!-- manager -->
		<?php if ($this->session->userdata('privilege') == '1' || $this->session->userdata('privilege') == '2' || $this->session->userdata('privilege') == '0' || $this->session->userdata('privilege') == '9') { ?>
			<li class="nav-item" <?= $this->session->userdata('privilege') == '2' ?>>
				<a class="nav-link" data-toggle="collapse" href="#setting" aria-expanded="false" aria-controls="setting">
					<i class="menu-icon typcn typcn-coffee"></i>
					<span class="menu-title">Settings</span>
					<i class="menu-arrow"></i>
				</a>
				<div class="collapse" id="setting">
					<ul class="nav flex-column sub-menu">
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('settings/titlesetting'); ?>">FAQ Name Setting</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('settings/unitsetting'); ?>">Unit Name Setting</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('settings/category'); ?>">Category Setting</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('settings/subcategory'); ?>">Sub Category Setting</a>
						</li>


					</ul>
				</div>
			</li>

			<li class="nav-item">
				<a class="nav-link" data-toggle="collapse" href="#manage_system" aria-expanded="false"
					aria-controls="manage_system">
					<i class="menu-icon typcn typcn-coffee"></i>
					<span class="menu-title">Manage System</span>
					<i class="menu-arrow"></i>
				</a>
				<div class="collapse" id="manage_system">
					<ul class="nav flex-column sub-menu">
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('ManageSystem/accountInformation'); ?>">Account
								Information</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('ManageSystem/unitInformation'); ?>">Unit Information</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('ManageSystem/counterQueuing'); ?>">Counter Queuing</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('ManageSystem/settingQueuing'); ?>">Counter Setting</a>
						</li>
					</ul>
				</div>
			</li>

		<?php } ?>
		<!-- akhir manager -->

		<!-- akhir asisten manager -->

		<li class="nav-item">
			<a class="nav-link" data-toggle="collapse" href="#faq_contents" aria-expanded="false"
				aria-controls="faq_contents">
				<i class="menu-icon typcn typcn-coffee"></i>
				<span class="menu-title">FAQ Content</span>
				<i class="menu-arrow"></i>
			</a>
			<div class="collapse" id="faq_contents">
				<ul class="nav flex-column sub-menu">
					<?php if ($this->session->userdata('privilege') == '2' || $this->session->userdata('privilege') == '1' || $this->session->userdata('privilege') == '0' || $this->session->userdata('privilege') == '5' || $this->session->userdata('privilege') == '9') { ?>
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('faq/create_faq'); ?>">Create FAQ</a>
						</li>
					<?php } ?>
					<li class="nav-item">
						<a class="nav-link" href="<?= site_url('faq'); ?>">FAQ</a>
					</li>
				</ul>
			</div>
		</li>
		<li class="nav-item">
			<a class="nav-link" data-toggle="collapse" href="#complain_content" aria-expanded="false"
				aria-controls="complain_content">
				<i class="menu-icon typcn typcn-coffee"></i>
				<span class="menu-title">Complaint Handling</span>
				<i class="menu-arrow"></i>
			</a>
			<div class="collapse" id="complain_content">
				<ul class="nav flex-column sub-menu">
					<?php if ($this->session->userdata('privilege') != '9') { ?>
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('ComplainHandling/create_complain'); ?>">Create
								Complaint</a>
						</li>
					<?php } ?>
					<li class="nav-item">
						<a class="nav-link" href="<?= site_url('ComplainHandling/inbox_complain'); ?>">My Inbox
							Complaint</a>
					</li>
					<?php if ($this->session->userdata('privilege') != 4) { ?>
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('ComplainHandling/group_inbox'); ?>">Group Inbox
								Complaint</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('ComplainHandling/inbox_caring'); ?>">Inbox Caring</a>
						</li>
					<?php } ?>
					<!--                    <li class="nav-item">
											<a class="nav-link" href="<?= site_url('ComplainHandling/my_inbox'); ?>">My Inbox</a>
										</li>-->
				</ul>
			</div>
		</li>

		<li class="nav-item">
			<a class="nav-link" data-toggle="collapse" href="#tracking" aria-expanded="false" aria-controls="tracking">
				<i class="menu-icon typcn typcn-coffee"></i>
				<span class="menu-title">Tracking</span>
				<i class="menu-arrow"></i>
			</a>
			<div class="collapse" id="tracking">
				<ul class="nav flex-column sub-menu">
					<li class="nav-item">
						<a class="nav-link" href="<?= site_url('Tracking/complain_history'); ?>">Complaint History</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="<?= site_url('Tracking/complain_history_sosmed'); ?>">Complaint
							History Social Media</a>
					</li>
				</ul>
			</div>
		</li>

		<!-- User Monitoring -->
		<?php if ($this->session->userdata('privilege') != '4') { ?>
			<?php if ($this->session->userdata('privilege') == '1' || $this->session->userdata('privilege') == '2' || $this->session->userdata('privilege') == '0' || $this->session->userdata('privilege') == '9') { ?>
				<li class="nav-item">
					<a class="nav-link" data-toggle="collapse" href="#user_monitoring" aria-expanded="false"
						aria-controls="user_monitoring">
						<i class="menu-icon typcn typcn-coffee"></i>
						<span class="menu-title">User Monitoring</span>
						<i class="menu-arrow"></i>
					</a>
					<div class="collapse" id="user_monitoring">
						<ul class="nav flex-column sub-menu">
							<li class="nav-item">
								<a class="nav-link" href="<?= site_url('UserMonitoring/recordings'); ?>">Recordings</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="<?= site_url('UserMonitoring/call_monitoring'); ?>">Call
									Monitoring</a>
							</li>

						</ul>
					</div>
				</li>
			<?php } ?>
		<?php } ?>
		<!-- Tutup User Monitoring -->

		<!-- User Monitoring -->
		<?php if ($this->session->userdata('privilege') != '4' && $this->session->userdata('privilege') != '5') { ?>
			<li class="nav-item">
				<a class="nav-link" data-toggle="collapse" href="#smartcall" aria-expanded="false"
					aria-controls="smartcall">
					<i class="menu-icon typcn typcn-coffee"></i>
					<span class="menu-title">Smart Call</span>
					<i class="menu-arrow"></i>
				</a>
				<div class="collapse" id="smartcall">
					<ul class="nav flex-column sub-menu">
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('SmartCall/call_number'); ?>">Call Number</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('SmartCall/auto_call'); ?>">Auto Call</a>
						</li>

					</ul>
				</div>
			</li>
		<?php } ?>
		<!-- Tutup User Monitoring -->

		<!-- IVR Campaign -->
		<?php if ($this->session->userdata('privilege') == '0' || $this->session->userdata('privilege') == '2' || $this->session->userdata('privilege') == '1' || $this->session->userdata('privilege') == '9') { ?>
			<li class="nav-item">
				<a class="nav-link" data-toggle="collapse" href="#ivrcampaign" aria-expanded="false"
					aria-controls="ivrcampaign">
					<i class="menu-icon typcn typcn-coffee"></i>
					<span class="menu-title">IVR Campaign</span>
					<i class="menu-arrow"></i>
				</a>
				<div class="collapse" id="ivrcampaign">
					<ul class="nav flex-column sub-menu">
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('IVRCampaign'); ?>">Campaign</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?= site_url('IVRCampaign/report_campaign_ivr'); ?>">Report
								Performance</a>
						</li>

					</ul>
				</div>
			</li>
		<?php } ?>
		<!-- Tutup IVR Campaign -->


		<?php if ($this->session->userdata('privilege') != '4') { ?>
			<li class="nav-item">
				<a class="nav-link" data-toggle="collapse" href="#reports" aria-expanded="false" aria-controls="reports">
					<i class="menu-icon typcn typcn-coffee"></i>
					<span class="menu-title">Reports</span>
					<i class="menu-arrow"></i>
				</a>
				<div class="collapse" id="reports">
					<ul class="nav flex-column sub-menu">
						<?php if ($this->session->userdata('privilege') != '4' && $this->session->userdata('privilege') != '3') { ?>
							<li class="nav-item">
								<a class="nav-link" href="<?= site_url('Reports/ch_group_report'); ?>">Complaint Handling
									Report</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="<?= site_url('Reports/call_center_report'); ?>">Call Center Report</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="<?= site_url('Reports/dashboard_history'); ?>">Dashboard History</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="<?= site_url('Reports/activation_log'); ?>">Activation Log</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="<?= site_url('Reports/activation_log_sosmed'); ?>">Activation Log
									Chatbot</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="<?= site_url('Reports/call_log'); ?>">Total Call Center Log</a>
							</li>
						<?php } ?>
						<?php if ($this->session->userdata('privilege') != '4') { ?>
							<li class="nav-item">
								<a class="nav-link" href="<?= site_url('Reports/ivr'); ?>">Smart Call Auto Call IVR</a>
							</li>
						<?php } ?>
					</ul>
				</div>
			</li>
		<?php } ?>


		<li class="nav-item">
			<a class="nav-link" data-toggle="collapse" href="#system" aria-expanded="false" aria-controls="system">
				<i class="menu-icon typcn typcn-coffee"></i>
				<span class="menu-title">System</span>
				<i class="menu-arrow"></i>
			</a>
			<div class="collapse" id="system">
				<ul class="nav flex-column sub-menu">
					<li class="nav-item">
						<a class="nav-link"
							href="<?= site_url('Systems/change_password?id=' . $this->session->userdata('id')); ?>">Change
							Password</a>
					</li>
					<!--                    <li class="nav-item">
											<a class="nav-link" href="<?= site_url('Systems/manually_report'); ?>">Manually Report</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" href="<?= site_url('Systems/broadcast_msg'); ?>">Broadcast Messages</a>
										</li>-->
					<li class="nav-item">
						<a class="nav-link logoutLink" href="javascript:void()">Log Out</a>
					</li>
				</ul>
			</div>
		</li>


		<li class="nav-item">
			<a class="nav-link" target="_blank" href="<?= site_url('Login/sso360') ?>">
				<i class="menu-icon typcn typcn-document-text"></i>
				<span class="menu-title">Goto Dashboard 360</span>
			</a>
		</li>


	</ul>
</nav>