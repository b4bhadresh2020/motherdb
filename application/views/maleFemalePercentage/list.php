

<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <section id="main-content">
                <div class="row card">

                    <div class="col-lg-6 col-md-12 countryWise">
                        <div class="title_type">
                            Country Wise
                        </div>
                        <div class="row">

                            <?php for ($i = 0; $i < sizeof($CountryWise); $i++) { ?>
                                <div class="col-lg-6 col-md-6 mainCircle">
                                    <div class="main_title">
                                        female / male - <?php echo $CountryWise[$i]['country']; ?>
                                    </div>
                                    <div class="circle">
                                        <span class="f_title">F</span>
                                        <span class="m_title">M</span>

                                        <div class="detail_circle">
                                            <div class="detail_circle_top">
                                                <div class="top_mini_left"><?php echo $CountryWise[$i]['female_per']; ?></div>
                                                <div class="top_mini_right"><?php echo $CountryWise[$i]['male_per']; ?></div>
                                            </div>
                                            <div class="detail_circle_bottom">
                                                <div class="top_mini_left"><?php echo number_format($CountryWise[$i]['female_count']); ?></div>
                                                <div class="top_mini_right"><?php echo number_format($CountryWise[$i]['male_count']); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="main_total">
                                        Total : <?php echo $CountryWise[$i]['total']; ?>
                                    </div>
                                </div>
                            <?php } ?>

                        </div>    
                    </div>


                    <div class="col-lg-6 col-md-12 groupWise">
                        <div class="title_type">
                            Group Wise
                        </div>
                        <div class="row">
                            
                            <?php for ($i = 0; $i < sizeof($GroupWise); $i++) { ?>
                                <div class="col-lg-6 col-md-6 mainCircle">
                                    <div class="main_title">
                                        female / male - <?php echo $GroupWise[$i]['groupName']; ?>
                                    </div>
                                    <div class="circle">
                                        <span class="f_title">F</span>
                                        <span class="m_title">M</span>
                                        <div class="detail_circle">
                                            <div class="detail_circle_top">
                                                <div class="top_mini_left"><?php echo $GroupWise[$i]['female_per']; ?></div>
                                                <div class="top_mini_right"><?php echo $GroupWise[$i]['male_per']; ?></div>
                                            </div>
                                            <div class="detail_circle_bottom">
                                                <div class="top_mini_left"><?php echo number_format($GroupWise[$i]['female_count']); ?></div>
                                                <div class="top_mini_right"><?php echo number_format($GroupWise[$i]['male_count']); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="main_total">
                                        Total : <?php echo $GroupWise[$i]['total']; ?>
                                    </div>
                                </div>
                            <?php } ?>
 
                        </div>    
                    </div>
                </div>        
            </section>
        </div>
    </div>
</div>


