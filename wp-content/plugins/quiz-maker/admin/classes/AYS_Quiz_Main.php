<?php

class AYS_Quiz_Main{
    public function AYS_Main(){
    ?>
    <div class="wrap">
        <h1 style="text-align: center;margin:15px;padding: 15px;">Welcome to Quiz Maker By AYS</h1>
        <div class="ac-home">
            <div class="ac-toolbar">
                <div class="ac-toolbar-block">
                    <div class="ac-toolbar-item">
                        <a href="?page=ays_quiz_quiz_categories">
                            <span class="ac-toolbar-icon ac-toolbar-icon-1 "></span>
                            <span class="ac-toolbar-text">Quiz Categories</span>				
                        </a>
                    </div>
                    <div class="ac-toolbar-item">
                        <a href="?page=ays_quiz_quizes">
                            <span class="ac-toolbar-icon ac-toolbar-icon-2"></span>
                            <span class="ac-toolbar-text">Quizes</span>
                        </a>
                    </div>
                </div>
                <div class="ac-toolbar-block">
                    <div class="ac-toolbar-item">
                        <a href="?page=ays_quiz_questions_categories">
                            <span class="ac-toolbar-icon ac-toolbar-icon-1 "></span>
                            <span class="ac-toolbar-text">Questions Categories</span>				
                        </a>
                    </div>
                    <div class="ac-toolbar-item">
                        <a href="?page=ays_quiz_questions">
                            <span class="ac-toolbar-icon ac-toolbar-icon-2"></span>
                            <span class="ac-toolbar-text">Questions</span>
                        </a>
                    </div>				
                </div>
            </div>
        </div>
    </div>
    <?php
    }
}
