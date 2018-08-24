<script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=ZQEAQICL6vg3MLfqP9yEYz3X"></script>
<script type="text/javascript">
    // 百度地图API功能
    var map = new BMap.Map("allmap");    // 创建Map实例
    var point = new BMap.Point(114.02597366,22.54605355);
    map.centerAndZoom(point, 14);  // 初始化地图,设置中心点坐标和地图级别

    var myIcon = new BMap.Icon("/themes/default/assets/images/employ/fox.gif", new BMap.Size(300,157));
    var marker = new BMap.Marker(point,{icon:myIcon});  // 创建标注

    map.addOverlay(marker);               // 将标注添加到地图中
//    marker.setAnimation(BMAP_ANIMATION_BOUNCE); //跳动的动画

    //添加地图类型控件
    map.addControl(new BMap.MapTypeControl({
        mapTypes:[
            BMAP_NORMAL_MAP,
            BMAP_HYBRID_MAP
        ]}));

    map.enableScrollWheelZoom(true);     //开启鼠标滚轮缩放
    map.setCurrentCity("深圳");
</script>
