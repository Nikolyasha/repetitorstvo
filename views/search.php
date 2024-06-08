<div class="search">
    <div class="search--wrapper container">
        <div class="search--input">
            <input type="text" <?php if($anketsOrVacancies=='ankets') echo 'placeholder="Поиск по анкетам"'; if($anketsOrVacancies=='vacancies')  echo 'placeholder="Поиск по вакансиям"'?>>
            <!-- <i class="fas fa-search"></i> -->
        </div>
        <div class="search--button">
            <button>
                <span>Поиск</span>
                <img src="/img/search.svg" alt="">
            </button>
        </div>
    
    </div>
</div>