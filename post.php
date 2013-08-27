==================шаблон - отопление========================
{{HEAD}}
{{CONTENT2}}
{{NAV}}
<td width="15%" valign="top">
    {{right-menu}}
    <a href="raschet"><img src="img/raschet.png" width="100%"></a>
    {{randompics}}
    [!Ditto? &tpl=`circle2` &documents=`[*parent*]` &display=`1`!]
    [!GlobalDitto? &tpl=`circle` &startID=`[*parent*]` &id=[*id*]!]
</td><td width="1%" ></td></tr>
{{FOOTER}}
=============================шаблон - Основной родители=================
{{HEAD}}
{{CONTENT}}
{{NAV}}
<td width="17%" valign="top">
    {{right-menu}}
    {{randompics}}
    [!Ditto? &tpl=`circle2` &documents=`[*parent*]` &display=`1`!]
    [!GlobalDitto? tpl=`circle` &startID=`[*parent*]` &id=[*id*]!]
</td><td width="1%" ></td></tr>
{{FOOTER}}

[!CacheAccelerator? &snippetToCache=`Ditto` &cacheId=`News_main_parents_11` &tpl=`circle2` &documents=`[*parent*]` &display=`1`!]
[!CacheAccelerator? &snippetToCache=`GlobalDitto` &cacheId=`News_main_parents_` &tpl=`circle` &startID=`[*parent*]` &id=[*id*]!]
22222222222222
===============================шаблон - Основной дочерние================
{{HEAD}}
{{CONTENT}}
{{NAV}}
<td width="15%" valign="top">
    {{right-menu}}

    {{randompics}}
    [!Ditto? &tpl=`circle2` &documents=`[*parent*]` &display=`1`!]
    [!GlobalDitto? tpl=`circle` &startID=`[*parent*]` &id=[*id*]!]
</td><td width="1%" ></td></tr>
{{FOOTER}}
==============================шаблон - Насосы сео=========================
{{HEAD}}


<div class="main">
    <table width="100%" cellspacing="0" border="0" align="left">
        <tr>
            <td width="1%"></td>
            <td width="55%" valign="top">

                <a href="/"><div class="header">
                        <h2><span>ПиЭйчИ Системс</span></h2>
                        <h1>PHE Systems. Professional. Handy. Excellent. </h1>
                    </div></a>
                <div class="content">
                    {{str_tovara}}
                    [*content*]


                </div>

            </td>
            <td width="1%"></td>

            {{NAV}}
            <td width="15%" valign="top">

                <div class="main-right">
                    <div class="round">
                        <div class="roundtl"><span></span></div>
                        <div class="roundtr"><span></span></div>
                        <div class="clearer"><span></span></div>
                    </div>
                    <div class="subnav">
                        <p>Теплообменники</p>
                        [!Wayfinder? &startId=`27` &level=`1` !]
                        <p>Каталог товаров</p>
                        [!Wayfinder? &startId=`28` &level=`1` !]
                        <p>Полезные статьи</p>
                        [!Wayfinder? &startId=`[*parent*]` &level=`1` !]

                    </div>
                    <div class="round">
                        <div class="roundbl"><span></span></div>
                        <div class="roundbr"><span></span></div>
                        <span class="clearer"></span>
                    </div>

                </div>
                [!Ditto? &tpl=`circle2` &documents=`[*parent*]` &display=`1`!]
                [!GlobalDittoPumps? tpl=`circle` &startID=`[*parent*]` &id=[*id*]!]
            </td><td width="1%"></td></tr>
        {{FOOTER}}
====================шаблон - насосы:товары===========================================
        {{head_prod}}


        <div class="main">
            <table width="100%" cellspacing="0" border="0" align="left">
                <tr>
                    <td width="1%"></td>
                    <td width="55%" valign="top">

                        {{header}}
                        <div class="content">
                            {{str_tovara}}
                            <h2>Технические характеристики</h2>
                            <table width="100%">

                                [+phx:if=`[*vendorcode*]`:is=``:then=``:else=`<tr><td width="30%"><i>Производитель</i></td><td>[*vendorcode*]</td></tr>`+]
                                [+phx:if=`[*country*]`:is=``:then=``:else=`<tr><td><i>Страна</i></td><td>[*country*]</td></tr>`+]
                                [+phx:if=`[*seria*]`:is=``:then=``:else=`<tr><td><i>Серия</i></td><td>[*seria*]</td></tr>`+]
                                [+phx:if=`[*model*]`:is=``:then=``:else=`<tr><td><i>Модель</i></td><td>[*model*]</td></tr>`+]
                                [+phx:if=`[*nasos*]`:is=``:then=``:else=`<tr><td><i>Тип</i></td><td>[*nasos*] [*tip*]</td></tr>`+]

                                [+phx:if=`[*system_type*]`:is=``:then=``:else=`<tr><td><i>Тип установки</i></td><td>[*system_type*]</td></tr>`+]
                                [+phx:if=`[*tip1*]`:is=``:then=``:else=`<tr><td><i>Категория</i></td><td>[*tip1*]</td></tr>`+]
                                [+phx:if=`[*oblast*]`:is=``:then=``:else=`<tr><td><i>Область применения</i></td><td>[*oblast*]</td></tr>`+]
                                [+phx:if=`[*whatfor*]`:is=``:then=``:else=`<tr><td><i>Область применения насоса</i></td><td>[*whatfor*]</td></tr>`+]
                                [+phx:if=`[*rashodnasosa*]`:is=``:then=``:else=`<tr><td><i>Расход (м3/ч)</i></td><td>[*rashodnasosa*]</td></tr>`+]
                                [+phx:if=`[*napor*]`:is=``:then=``:else=`<tr><td><i>Напор (м)</i></td><td>[*napor*]</td></tr>`+]
                                [+phx:if=`[*capacity*]`:is=``:then=``:else=`<tr><td><i>Мощность (кВт)</i></td><td>[*capacity*]</td></tr>`+]
                                [+phx:if=`[*prisoed*]`:is=``:then=``:else=`<tr><td><i>Тип присоединения</i></td><td>[*prisoed*]</td></tr>`+]
                                [+phx:if=`[*diametr_prohoda*]`:is=``:then=``:else=`<tr><td><i>Диаметр свободного прохода (мм)</i></td><td>[*diametr_prohoda*]</td></tr>`+]
                                [+phx:if=`[*mint*]`:is=``:then=``:else=`<tr><td><i>Минимальная температура (°C)</i></td><td>[*mint*]</td></tr>`+]
                                [+phx:if=`[*maxt*]`:is=``:then=``:else=`<tr><td><i>Максимальная температура (°C)</i></td><td>[*maxt*]</td></tr>`+]
                                [+phx:if=`[*max_izb*]`:is=``:then=``:else=`<tr><td><i>Максимальное давление (бар)</i></td><td>[*max_izb*]</td></tr>`+]
                                [+phx:if=`[*glubina*]`:is=``:then=``:else=`<tr><td><i>Глубина погружения (м)</i></td><td>[*glubina*]</td></tr>`+]
                                [+phx:if=`[*maradan*]`:is=``:then=``:else=`<tr><td><i>Рабочее давление с напорной стороны (бар)</i></td><td>[*maradan*]</td></tr>`+]
                                [+phx:if=`[*marada*]`:is=``:then=``:else=`<tr><td><i>Номинальное давление (бар)</i></td><td>[*marada*]</td></tr>`+]
                                [+phx:if=`[*chavr*]`:is=``:then=``:else=`<tr><td><i>Частота вращения (Гц)</i></td><td>[*chavr*]</td></tr>`+]
                                [+phx:if=`[*chastota*]`:is=``:then=``:else=`<tr><td><i>Электропитание (Гц)</i></td><td>[*chastota*]</td></tr>`+]
                                [+phx:if=`[*chadvi*]`:is=``:then=``:else=`<tr><td><i>Частота двигателя привода (Гц)</i></td><td>[*chadvi*]</td></tr>`+]
                                [+phx:if=`[*gabarit*]`:is=``:then=``:else=`<tr><td><i>Габариты (мм)</i></td><td>[*gabarit*]</td></tr>`+]
                                [+phx:if=`[*weight*]`:is=``:then=``:else=`<tr><td><i>Масса (кг)</i></td><td>[*weight*]</td></tr>`+]
                                [+phx:if=`[*patrubok*]`:is=``:then=``:else=`<tr><td><i>Диаметр патрубков</i></td><td>[*patrubok*]</td></tr>`+]
                                [+phx:if=`[*koleso*]`:is=``:then=``:else=`<tr><td><i>Тип рабочего колеса</i></td><td>[*koleso*]</td></tr>`+]
                                [+phx:if=`[*ph*]`:is=``:then=``:else=`<tr><td><i>показатель ph</i></td><td>[*ph*]</td></tr>`+]
                                [+phx:if=`[*ustagr*]`:is=``:then=``:else=`<tr><td><i>Установка агрегата</i></td><td>[*ustagr*]</td></tr>`+]
                                [+phx:if=`[*tipriv*]`:is=``:then=``:else=`<tr><td><i>Тип привода</i></td><td>[*tipriv*]</td></tr>`+]
                                [+phx:if=`[*tipod*]`:is=``:then=``:else=`<tr><td><i>Тип подшипника</i></td><td>[*tipod*]</td></tr>`+]
                                [+phx:if=`[*rotor*]`:is=``:then=``:else=`<tr><td><i>Тип ротора</i></td><td>[*rotor*]</td></tr>`+]
                                [+phx:if=`[*razko*]`:is=``:then=``:else=`<tr><td><i>Разделение корпуса</i></td><td>[*razko*]</td></tr>`+]
                                [+phx:if=`[*protect*]`:is=``:then=``:else=`<tr><td><i>Класс защиты</i></td><td>[*protect*]</td></tr>`+]
                                [+phx:if=`[*material*]`:is=``:then=``:else=`<tr><td><i>Материал корпуса</i></td><td>[*material*]</td></tr>`+]
                                [+phx:if=`[*more*]`:is=``:then=``:else=`<tr><td><i>Дополнительные характеристики</i></td><td>[*more*]</td></tr>`+]
                            </table>
                            [*content*]


                        </div>

                    </td>
                    <td width="1%"></td>

                    {{NAV}}
                    <td width="15%" valign="top">

                        {{menu-nasos}}

                        [!GlobalDittoPumps? tpl=`circle` &startID=`[*parent*]` &id=[*id*]!]
                    </td><td width="1%"></td></tr>
                {{FOOTER}}
===============================шаблон насосы:серии======================
                {{HEAD}}


                <div class="main">
                    <table width="100%" cellspacing="0" border="0" align="left">
                        <tr>
                            <td width="1%"></td>
                            <td width="55%" valign="top">

                                <a href="/"><div class="header">
                                        <h2><span>ПиЭйчИ Системс</span></h2>
                                        <h1>PHE Systems. Professional. Handy. Excellent. </h1>
                                    </div></a>
                                <div class="content">
                                    {{str_tovara}}
                                    [*content*]


                                </div>

                            </td>
                            <td width="1%"></td>

                            {{NAV}}
                            <td width="15%" valign="top">

                                <div class="main-right">
                                    <div class="round">
                                        <div class="roundtl"><span></span></div>
                                        <div class="roundtr"><span></span></div>
                                        <div class="clearer"><span></span></div>
                                    </div>
                                    <div class="subnav">
                                        <p>Теплообменники</p>
                                        [!Wayfinder? &startId=`27` &level=`1` !]
                                        <p>Каталог товаров</p>
                                        [!Wayfinder? &startId=`28` &level=`1` !]
                                        <p>Полезные статьи</p>
                                        [!Wayfinder? &startId=`[*parent*]` &level=`1` !]

                                    </div>
                                    <div class="round">
                                        <div class="roundbl"><span></span></div>
                                        <div class="roundbr"><span></span></div>
                                        <span class="clearer"></span>
                                    </div>

                                </div>

                                [!GlobalDittoPumps? tpl=`circle` &startID=`[*parent*]` &id=[*id*]!]
                            </td><td width="1%"></td></tr>
                        {{FOOTER}}
=====================шаблон - str_tovara=============================
                        {{HEAD}}


                        <div class="main">
                            <table width="100%" cellspacing="0" border="0" align="left">
                                <tr>
                                    <td width="1%"></td>
                                    <td width="55%" valign="top">

                                        <a href="/"><div class="header">
                                                <h2><span>ПиЭйчИ Системс</span></h2>
                                                <h1>PHE Systems. Professional. Handy. Excellent. </h1>
                                            </div></a>
                                        <div class="content">
                                            {{str_tovara}}
                                            [*content*]


                                        </div>

                                    </td>
                                    <td width="1%"></td>

                                    {{NAV}}
                                    <td width="15%" valign="top">

                                        <div class="main-right">
                                            <div class="round">
                                                <div class="roundtl"><span></span></div>
                                                <div class="roundtr"><span></span></div>
                                                <div class="clearer"><span></span></div>
                                            </div>
                                            <div class="subnav">
                                                <p>Теплообменники</p>
                                                [!Wayfinder? &startId=`27` &level=`1` !]
                                                <p>Каталог товаров</p>
                                                [!Wayfinder? &startId=`28` &level=`1` !]
                                                <p>Полезные статьи</p>
                                                [!Wayfinder? &startId=`[*parent*]` &level=`1` !]

                                            </div>
                                            <div class="round">
                                                <div class="roundbl"><span></span></div>
                                                <div class="roundbr"><span></span></div>
                                                <span class="clearer"></span>
                                            </div>

                                        </div>

                                        [!GlobalDitto? tpl=`circle` &startID=`[*parent*]` &id=[*id*]!]
                                    </td><td width="1%"></td></tr>
                                {{FOOTER}}



                                [!Wayfinder? &startId=`270` !]