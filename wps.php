<?php
header('Content-type: text/xml');

$service = $_GET['service'];
$request = $_GET['request'];
if ($service == "wps") {
    if ($request == "GetCapabilities") {
        echo '<wfs:WFS_Capabilities xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.opengis.net/wfs" xmlns:wfs="http://www.opengis.net/wfs" xmlns:ows="http://www.opengis.net/ows" xmlns:wps="http://www.opengis.net/wps/1.0.0" xmlns:gml="http://www.opengis.net/gml" xmlns:ogc="http://www.opengis.net/ogc" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:cite="http://www.opengeospatial.net/cite" updateSequence="165">
<ows:ServiceIdentification>
<ows:Title>convert utm_y / utm_x to lat/long</ows:Title>
<ows:Abstract>
This is the reference implementation of WPS 1.0.0, supports possible Transaction.
</ows:Abstract>
<ows:ServiceType>WPS</ows:ServiceType>
<ows:ServiceTypeVersion>1.1.0</ows:ServiceTypeVersion>
<ows:Fees>FREE</ows:Fees>
<ows:AccessConstraints>NONE</ows:AccessConstraints>
</ows:ServiceIdentification>
<ows:ServiceProvider>
<ows:ProviderName>UOT</ows:ProviderName>

</ows:ServiceProvider>
<ows:OperationsMetadata>
<ows:Operation name="GetCapabilities">
<ows:DCP>
<ows:HTTP>
<ows:Get xlink:href="http://localhost/transition/index.php"/>
</ows:HTTP>
</ows:DCP>
</ows:Operation>
<ows:Operation name="DescribeProcess">
<ows:DCP>
<ows:HTTP>
<ows:Get xlink:href="http://localhost/transition/index.php?REQUEST=DescribeProcess"/>
</ows:HTTP>
</ows:DCP>

</ows:Operation>
<ows:Operation name="Execute">
<ows:DCP>
<ows:HTTP>
<ows:Get xlink:href="http://localhost/transition/index.php?REQUEST=Execute"/>
</ows:HTTP>
</ows:DCP>

</ows:Operation>
</ows:OperationsMetadata>
<wps:ProcessOfferings>
	<wps:Process wps:processVersion="1.0.0">
		<ows:Identifier>convert</ows:Identifier>
		<ows:Title>convert E/N to L/L</ows:Title>
		<ows:Abstract>
			convert E/N to L/L
		</ows:Abstract>
		<ows:Metadata xlink:title="convert"/>
		<ows:Metadata xlink:title="point"/>
	</wps:Process>
</wps:ProcessOfferings>
</wfs:WFS_Capabilities>';
    } elseif ($request == "Execute") {


        if (isset($_GET['utm_x'])) {


            $utm_x = $_GET['utm_x'];
            $utm_y = $_GET['utm_y'];
            $zone = $_GET['zone'];


            function convert($utm_x, $utm_y, $utmZone)
            {
                // This is the lambda knot value in the reference
                $LngOrigin = Deg2Rad($utmZone * 6 - 183);

                // The following set of class constants define characteristics of the
                // ellipsoid, as defined my the WGS84 datum.  These values need to be
                // changed if a different dataum is used.

                $FalseNorth = 0;   // South or North?
                //if (lat < 0.) FalseNorth = 10000000.  // South or North?
                //else          FalseNorth = 0.

                $Ecc = 0.081819190842622;       // Eccentricity
                $EccSq = $Ecc * $Ecc;
                $Ecc2Sq = $EccSq / (1. - $EccSq);
                $Ecc2 = sqrt($Ecc2Sq);      // Secondary eccentricity
                $E1 = (1 - sqrt(1 - $EccSq)) / (1 + sqrt(1 - $EccSq));
                $E12 = $E1 * $E1;
                $E13 = $E12 * $E1;
                $E14 = $E13 * $E1;

                $SemiMajor = 6378137.0;         // Ellipsoidal semi-major axis (Meters)
                $FalseEast = 500000.0;          // UTM East bias (Meters)
                $ScaleFactor = 0.9996;          // Scale at natural origin

                // Calculate the Cassini projection parameters

                $M1 = ($utm_x - $FalseNorth) / $ScaleFactor;
                $Mu1 = $M1 / ($SemiMajor * (1 - $EccSq / 4.0 - 3.0 * $EccSq * $EccSq / 64.0 - 5.0 * $EccSq * $EccSq * $EccSq / 256.0));

                $Phi1 = $Mu1 + (3.0 * $E1 / 2.0 - 27.0 * $E13 / 32.0) * sin(2.0 * $Mu1);
                +(21.0 * $E12 / 16.0 - 55.0 * $E14 / 32.0) * sin(4.0 * $Mu1);
                +(151.0 * $E13 / 96.0) * sin(6.0 * $Mu1);
                +(1097.0 * $E14 / 512.0) * sin(8.0 * $Mu1);

                $sin2phi1 = sin($Phi1) * sin($Phi1);
                $Rho1 = ($SemiMajor * (1.0 - $EccSq)) / pow(1.0 - $EccSq * $sin2phi1, 1.5);
                $Nu1 = $SemiMajor / sqrt(1.0 - $EccSq * $sin2phi1);

                // Compute parameters as defined in the POSC specification.  T, C and D

                $T1 = tan($Phi1) * tan($Phi1);
                $T12 = $T1 * $T1;
                $C1 = $Ecc2Sq * cos($Phi1) * cos($Phi1);
                $C12 = $C1 * $C1;
                $D = ($utm_y - $FalseEast) / ($ScaleFactor * $Nu1);
                $D2 = $D * $D;
                $D3 = $D2 * $D;
                $D4 = $D3 * $D;
                $D5 = $D4 * $D;
                $D6 = $D5 * $D;

                // Compute the Latitude and Longitude and convert to degrees
                $lat = $Phi1 - $Nu1 * tan($Phi1) / $Rho1 * ($D2 / 2.0 - (5.0 + 3.0 * $T1 + 10.0 * $C1 - 4.0 * $C12 - 9.0 * $Ecc2Sq) * $D4 / 24.0 + (61.0 + 90.0 * $T1 + 298.0 * $C1 + 45.0 * $T12 - 252.0 * $Ecc2Sq - 3.0 * $C12) * $D6 / 720.0);

                $lat = Rad2Deg($lat);

                $lon = $LngOrigin + ($D - (1.0 + (2.0 * $T1) + $C1) * $D3 / 6.0 + (5.0 - 2.0 * $C1 + 28.0 * $T1 - 3.0 * $C12 + 8.0 * $Ecc2Sq + 24.0 * $T12) * $D5 / 120.0) / cos($Phi1);

                $lon = Rad2Deg($lon);

                // Create a object to store the calculated Latitude and Longitude values
                $PC_LatLon['lat'] = $lat;
                $PC_LatLon['lon'] = $lon;

                // Returns a PC_LatLon object
                return $PC_LatLon;
            }


            $output = convert($utm_x, $utm_y, $zone);


            echo '<gml:point xmlns:gml="http://www.opengis.net/gml/3.2" xmlns:xlink="http://www.w3.org/1999/xlink" gml:id="convert">
<gml:description xlink:title="convert" xlink:type="simple">convert N/E to L/L</gml:description>
<gml:identifier>convert</gml:identifier>
<gml:exterior>
<gml:AbstractRing xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:type="gml:RingType">
<gml:curveMember xlink:type="simple">
<gml:respoin xsi:type="gml:poin">
<gml:segments>
<gml:AbstractpointSegment xsi:type="gml:LineStringSegmentType" interpolation="linear">
<gml:lat>' . $output['lat'] . '
</gml:lat>
<gml:long>' . $output['lon'] . '
</gml:long>
</gml:AbstractpointSegment>
</gml:segments>
</gml:respoin>
</gml:curveMember>
</gml:AbstractRing>
</gml:exterior>
</gml:point>';
        } else {
            echo "<?xml version='1.0' standalone='yes'?> <error>you must enter E and N and zone </error>";
        }

    } elseif ($request == 'DescribeProcess') {
        echo '<wps:ProcessDescriptions xmlns:ows="http://www.opengis.net/ows/1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:wps="http://www.opengis.net/wps/1.0.0">
<ProcessDescription statusSupported="false" storeSupported="true" wps:processVersion="1.0.0">
<ows:Identifier>convert</ows:Identifier>
<ows:Title>convert E/N to L/L</ows:Title>
<ows:Abstract>
convert E/N to L/L
</ows:Abstract>
<ows:Metadata xlink:title="spatial"/>
<ows:Metadata xlink:title="geometry"/>
<ows:Metadata xlink:title="convert"/>
<ows:Metadata xlink:title="GML"/>
<wps:Profile>supermap:wps:1.0.0:convert</wps:Profile>
<DataInputs>
<Input maxOccurs="1" minOccurs="1">
<ows:Identifier>utm_y</ows:Identifier>
<ows:Title>
East
</ows:Title>
<ows:Abstract>East</ows:Abstract>

</Input>
<Input maxOccurs="1" minOccurs="0">
<ows:Identifier>utm_x</ows:Identifier>
<ows:Title>utm_x</ows:Title>
<ows:Abstract>utm_x</ows:Abstract>
</Input>

<Input maxOccurs="1" minOccurs="0">
<ows:Identifier>zone</ows:Identifier>
<ows:Title>zone</ows:Title>
<ows:Abstract>zone</ows:Abstract>
</Input>
</DataInputs>
<ProcessOutputs>
<Output>
<ows:Identifier>lat</ows:Identifier>
<ows:Title>lat</ows:Title>
<ows:Abstract>
lat
</ows:Abstract>

</Output>
<Output>
<ows:Identifier>lon</ows:Identifier>
<ows:Title>lon</ows:Title>
<ows:Abstract>
lon
</ows:Abstract>

</Output>
</ProcessOutputs>
</ProcessDescription>
</wps:ProcessDescriptions>';
    } else {
        echo "<?xml version='1.0' standalone='yes'?> <error>you must enter Request </error>";
    }

} else {
    echo "<?xml version='1.0' standalone='yes'?> <error>you must enter Service </error>";
}
?>
