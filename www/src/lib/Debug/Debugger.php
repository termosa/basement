<?php

/**
 * Отладчик
 *
 * Перехватывает ошибки и выводит панель отладки (первое еще не реализовано)
 */
class Debug_Debugger
{
	private $start_time = NULL;
	private $end_time = NULL;
	/**
	 * Запускает буфер
	 */
	function __construct() {
		$this->start_time = microtime();
		// $this->start_time = $_SERVER['REQUEST_TIME'];
		ob_start();
	}

	public function getDiff( $s ) {
		$s = explode( ' ', $s );
		$f = explode( ' ', $this->end_time );

		$sm = (double) $s[0];
		$fm = (double) $f[0];

		$ss = (int) $s[1];
		$fs = (int) $f[1];

		$sec = $fs - $ss;
		$mic = $fm - $sm;

		if ($mic < 0) {
			$mic = 1 + $mic;
			$sec--;
		}

		$mic = substr($mic, 2);

		return $sec . '.' . $mic;
	}

	function showVarPanel( $head, $link, $data ) {
		?>

			<a href="javascript:DebugPanel_Toggle('<?php echo $link?>')"><?php echo $head?> <span>(<?php echo count($data)?>)</span></a>

			<div id="<?php echo $link?>" style="display: none">
				<table cellpadding="0" cellspacing="0">
					<tr>
						<th>№</th>
						<th>key</th>
						<th>value</th>
					</tr>
					<?php if (count($data)):?>
						<?php $i = 0; foreach ($data as $k => $v):?>
							<tr>
								<td class="num"><?php echo ++$i?></td>
								<td><?php echo $k?></td>
								<td><?php echo $this->VarDump($v)?></td>
							</tr>
						<?php endforeach?>
					<?php else:?>
						<tr>
							<td colspan="3" class="center">Empty</td>
						</tr>
					<?php endif?>
				</table>
			</div>

	<?php
	}

	function VarDump($var)
	{
		$ret = '';
		if (is_bool($var))
		{
			$ret = ($var) ? 'true' : 'false';
		}
		elseif (is_scalar($var))
		{
			$ret = htmlspecialchars($var);
		}
		elseif (is_null($var))
		{
			$ret = 'NULL';
		}
		else
		{
			ob_start();
			$data = ob_get_clean();
			$data = preg_replace('/=>\n\s+/', ' => ', $data);
			$data = htmlspecialchars($data);
			$ret = '<pre>' . $data . '</pre>';
		}
		return $ret;
	}

	/**
	 * Выводит панель отладки
	 *
	 * Собирает данные из буфера и дописывает скрипт в тег head
	 */
	function __destruct() {
		$this->end_time = microtime();
		global $_db, $_query;
		$content = ob_get_contents();
		ob_end_clean();

		ob_start();
		chdir(BASEPATH);
		
		inc('Debug_DebugPanel');
		$debug = new Debug_DebugPanel;
		?>

			<style type="text/css">

				/**
				 * Стилизация панели для дебаг-панели внизу страницы
				 *
				 * Данный файл подключается вне пакера и не проходит обработку less-движком
				 *
				 * @author Zmi
				 */


				#i-debug-panel
				{
					float: right;
					background: #181818 url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAAAAACMmsGiAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAABdJREFUCB1jUlGRkWH6CQRMf//y8oJZAFUaCmUwcfODAAAAAElFTkSuQmCC");
					font-size: 12px;
					color: silver;
					min-height: 22px;
					position: absolute;
					right: 0;
					margin-top: 15px;

					border-radius: 3px 0 0 3px;
					-moz-border-radius: 3px 0 0 3px;
					-webkit-border-radius: 3px 0 0 3px;
					-o-border-radius: 3px 0 0 3px;
					-ms-border-radius: 3px 0 0 3px;
					-khtml-border-radius: 3px 0 0 3px;
				}

				#i-debug-panel ul, #i-debug-panel li
				{
					padding: 0;
					margin: 0;
					line-height: 2;
					list-style: none;
				}

				#i-debug-panel-list li
				{
					padding: 0 10px;
					float: left;
					border-right: 1px solid #444;;
				}

				#i-debug-panel-list li:last-child
				{
					border-right: none 0;
				}

				#i-debug-panel-list .show-hide-elem
				{
					border-bottom: none 0 !important;
				}

				#i-debug-panel a
				{
					color: #DDD;
					text-decoration: none;
				}

				#i-debug-panel a:hover
				{
					color: #DDD;
					text-decoration: underline;
				}

				#i-debug-panel a span
				{
					color: silver;
				}

				#i-debug-panel a span.small
				{
					font-size: 10px;
					color: silver;
				}

				#i-debug-panel table
				{
					font-size: 9pt;
					color: rgb(163, 163, 163);
					font-weight: normal;
					border: 0;
					padding: 0;
					border-collapse: collapse;
					margin: 10px auto;
				}

				#i-debug-panel table th
				{
					padding: 0 5px;
				}

				#i-debug-panel table td
				{
					padding:1px 5px;
					text-align: left;
					vertical-align: top;
					border: 1px dotted #424242;
				}

				#i-debug-panel table td.center
				{
					text-align: center;
				}

				#i-debug-panel table td.num
				{
					border: none;
				}

				#i-debug-panel table tr.total td
				{
					border: none;
					border-top: 1px solid #424242;
					border-bottom: 1px solid #424242;
					color: #DDD;
				}

				#i-debug-panel table td span
				{
					color: silver;
				}

				#i-debug-panel table tr:hover
				{
					background: #252525;
				}

				#i-debug-panel span.icon
				{
					background: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMAAAAAQCAYAAABA4nAoAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAFodJREFUeNrUWgl4FUW2Pt13X7InhIQQAlkIZoEYhOCCIKgIYRAYwJmHjBoFZB8RUEYYBAFRvqci7iDPAXSYGT9kkAA6DruskbAjISEQkkCWm/Xu3X3fOdV9b25uEgbB73vf66S+2111TnVVnfOfpao5j8cDd3ONX7KkveqRWGI6YPm3KIqXJVFkD1tXrLir9/NvHQys+g2Wd9shnYPln/4V0vwHYeC6yjaEKpXqE/zJVh4LcLxTAmn2Py9Pb/z48bcz58CLXrqdbg52meSrdLhE0Kr5DwRJek7ygB5INBzOkQOHmuc/dwnSdL1W5aOveCcX7vaq/pBrUxcSAukqFczG215K1QUU13sNDXA2kDZqmgf+P1/qX7tDUVbsLl8tXfqRvaQE+K5dQUCQEdAOHj8OX3z33YuyWKHoV3mhWwismXVfZmpUfEy02VtxrfJm8/HTF2cFAqCjNcE5ZE94NCabbMPf/lXpXSehPWKn0+m9jVm3bt0nVqsV7HY7CIII1dXNeC+BXm+AoKAgCA4ORuUKhqlTJ0xvp6vHsOwemNkT7kvuDqFBGnmVcAz1TW798aIr0/7104VpWPM4lu/8GXNzZSBoNBrgOI6ttVqtHoBVmQrJaUEQDnvb3G43q/z222/bm1K61a3/yNR7QIo6slM4jUGorkq2njicihAl2Z1td9EyEM8cgpMngHKdcSA0qH5Y4hSS61iO4QDwpZ4bIKGeeEQY02tjD6zvjyXoF0q+CctR5f6O+dVjxoyhxerrZ/Fu9yrART2hzswMtJ7gdrm46loHmHCxq2ocqKMioFWD5uZm+HrZso9GL1w4TSG/axD88NpgGLJ4t39VgkmrNl+5csVXgYpnBpcroT1+QVEGvytCq+FVzc0iEABUnKhyuqRIrL/xn8aC6wENaCZra2sZAKqqmsDhlMBkCgJBFFA3eDCaDNC+8vO7p+UOhP5drdA/fD900jXA6rX5MHfGcKhyhEDPsF6QHDsYNnx3ECcrtQGBct2PpTcBHUHcZ8uWLR+Ssk+cOJHW+5riHU9h+dHLYLG06WN2p7T0FD56Uiexah3DoOjxdIKENLAcLCCv8EIrD9B2DMOMBvX0vn3ieqYkRkabTTpSA5S90/pzcfWgn06Xj7LZ3R8g3S7FYA6YPPmlVyMiItIEwYVrLrCCLYghXBWeJ/1EcKvxV4NFhTqmxrWtOrd69eqV1Mfd8JNli4yKihqycuXKNx0OB+qJk1k1Km63CwUpKQZbpRhC6kwPa9e++UpNzc1SrKgJdP/40ofOXDkLblSEtD5miIkKATd6hr242ms3b4YhaWkfrtm8eRGSvqGer35HEdovvU4Jbwl/ZCBY+jgMmb/NW+8hRVQ8kU8xweFo5avHZo9i5lVwuQL77RMZbDA0NLo81B6k5wzWZlcW1u/8xSPkmAEHDyfJBeTif5FFxtBmd96wByA36QbkxBSDKPDgEE0wffo4sGEsFKRxwIjYAgjXJIPrkQHw+e79uzFUCoxdHkhJSRn38ssvz16wYEFYdXV1Q01Ntewk3W4eZfyHVatWLUehv3fu3DniPURtVVVtRt3LXFmlddS8AsbIUCZ5a3U9GgqtFml7BRL3DFD+8BD9wjG5GVmSKJkdNhfcrKxWwqqQ4JTu4cGJCeEx/9x5LqSu3kbVu7Zt22YaPnx4WkUFz5SVDOi+siduY3FD08L7frppw6LoKYH8VALvvz3/YLv8alSOqNTU1F5e60UAICA4nQSGQABoGADCwiKhW7fEXjdulEdxoljjDXkcBw4Al5PTKuQ5du4yJMXFQ1hQKAx9aDjzBOSqdxQU1NMwSoTv+swaN2sQU9LbjdsQ0Wv+vqaVAvzw1igYMmsLC+3dbNwt/dEzTsqneWMfmOBrq6+p8d3r9HrQ6HRPRIYGBzc0OmykvJEh2pDrlY2/cTscO/3CHbySbmOkHkXxPYryi6wEXB8MSE2CvrE2yOlyDZ58+iuYND4HRg7PwPUXQPJIbN0duG4PdC6F8uYguID0BZdLyIpO9wEc4N5p06bNvnq1FN54Y9nykycLL0uSjPmpU6fOQRknURvRTJkypdgLgLq6NoPWd0mPC60T1BDSNYrWDUSuHMJC9aEn88v0t5hsjFGvmjFqWK+sxoYmM727qakJdryfwRpHzDzDwkCe58yPDU7K+mf+2Rl2h3gKIxAuLy+vVUf5r12D16eEg4RzJuWl0E2UWhsOFdYv/rgW2uNv79py/BosnRrh68fLTwCIjomJif+lxi08PCqeeFWieIEQUlTSDF1xoKVXHUz56T0U8vz+iSdg+48/QliwBj1BKPMEagQAhh68YiX5xsZGKH+3nCk2xc8wDkX+jabD54R5CRSR823CoTUTYMgLX0gudPtuv9CGnsEuA2Ds0D/calrpILof6hxuiCq8UFWKQuSSupoSOEnIUeLZY7cKCVesWPFt7969c0lwNptNCYUEzAH0YDabyQrCpUuXyPWf8OVMkpTXIzYaHolDnfQYYNH8XFjx37sgoXsn6J3WFcdO3kwWmhtB9EjXMjh0LQmOXbqc5wUAGS6dTrd17ty5YYsXL3odrT/cc0+vJEmSwUb3tMZarRaQ5s9o4LZ6wdwK08o8jP1OFn8zWZtmCtFqOVHg7DaPe+THzjNf5EJ9h7OXxNze6TE9GxubzA6H7FVdft6V7pksWa6iNqckhvc8daYiF9eHo/XybsaQsjc01KP8ghD8Hvjs6xrol26E3j2Nrby6DudCdLoAfm8f+9C+7vrRAitn9gB5jbBPIcQ3Ji+/GhUlulu3bqTM4A2BOvYAIvMARBsdHRdPvCrZ+nDJ3U3grOSgR7yOWXlS9IMFcshD19nzRfD73DHYhgPl3T4AoG3kmxqb2DsxjmNKo3apQXSIHT4Tvcfl8QJgGIVS3sn/7vjWkItDH2DCbnVZbdLYUS/eckMJPcW8lISIGLvD7i65Wst8d2JXU2x8tD72QkndfOqe6WH7V/bixYtz/ZPgqppGsKIyaHVaMAeZIAST4EdHjB42a+pz271g8gCn06q1EBmG4ZhTA1mZSfDayyNh2apvEAxPQmZGvOwJ2LpxEIV0RE98fu9+GGXVt6KiwlpYWFicnZ2deOTIkYsrVqz8NzUuXPjqI5mZmakFBQXFRINVExQQ7mtnHlPS0tLeTnt0xEitzsASFpfTbk87sGMHsszr0NeJQk5sZ3N0Q31jCyZQD3o+sZUBWI0xuCjICqzCODwkJCyaeFCXaJeN0SrhM9TX16FCxzFFPnGmEgvAs6O7QN+0YJ9np9CG6CIEgffnJ54fjtbAG58WKWOQUz+iFYQuPsPo5VfjwkXGxcUlknLRDgUpPVkH+iVi2foQAPhWeQAKOJF41fKA+JJrNojDwZdedzE0EiAH3pfLPAE9X6k+DVHhOnbP4ct9HgC9XGNTI5DVokKX0WEEW7Wtw2eiB6fsAfDtk9NH5HbvfM894fRcXotJ+FcbGN0Za0l9Q2dZGPfEVoT6W168XqMY1C8HuN9k1GYldw/rvP9waZHb6fg7ybXgVEVEv3u7pJRcs6Q2NTuHeJO320mCb9Y0gBX7N2Di65ZC2QaJwahrw2dH2g2f7YJJz44CsjdZ96bAskUTYOGSzbBw3ljI6t0dOASASs3BhnXbwN65deSNcuj/6aefrKKQg7wNgWXlSkQQwKvUjvcrN2/e9ArmCIkrVixfTaHI5MlTFhAAfqdVcieVDp7m/yq7wXQoLNibX3LvwyPS6Pmnffkl6enphfnoIOnaKD2FtlB2HeN82i7EUY5Pykhzb2hsBjIGX74Rz5TyqT9dB16tUwJDyksEI/Hgmp0kHq91J8VMidfAUy8d8s3v5NdPQNbYnTD1qR7QPwOjCFxnDUYD5NVCFABQIfDsPnQDFr1/Ft6dnw5z3jrL+FhikxjC9NkLAArDiZ8AoKdFKy6+zBpJ8b0FB6kgyx8AMgjCwyOJRq+VO+ST4o3gQA+QGK9nrosU3esJKOQ5fcUJlgZBietElpjJWgOqLiIHTS+EQrLOBEVOq/z7grbDZ6JHALANcVTfeHOVK7x6x998e4mPKQv3yZjSYu3sodndu3fHpxQEQEos1ZdWXLYcPrVnMimzuyUGGB6TFBl5rri2oays9opKrf6EAarCMuxipLlTWKQp0lLbOPRWAGibBLfE/hIntpsDcCwMccOz034Lkt3Ddi7Ao4K5r26Alcv+gB4hkcmBx744/CO6Q1+5gWu9FatyOJxM8chgcZyadjrctNsh77QIbqqjNqIhWuIJHO5Gz+9RvJTncfnJJwdP9xjkPZ5zJ080buz2dj5TXdHNrFbbEEjgKJyxO9EINDlQzhjbanRM+Q3oSFRaPcpd00LOIZqRh/SADIfXspMOrl3Ym/FR8eYABIJeI76Bv7zZD8NGkcXyNA+31MJPIfK8twvgo0V90Qu7EAQZCtzIM3jg6tUqCDLrQG/QMMAQPwFARS774sWf4fz5c+DtTN5JkUAOrXif5adS92YpTDo0kwCgUrYR+SvldoilHKDcyXjICnk9gcfjAIOW3LdW9gA4KR8A0JL/KfU+GLb7r7Dx8SG3/bth525eAQC4KHRrZ5seIwdQ40LVYKJLlrnloCc03OUAlvc4EZy8laICyOQw+b16qawS5/QlAsCmbJN+WXa9NjEiKa6L23WVZXSSyXT7u0C+HSAEgVICSFx1zc3a2gYtRJhEtj0uovLs+nYlLRwLQT2ovPiPFhSgtkEFSM/4/DzAiWeeeWYR28OcPSfvwQcHJSxd+tbI119fyIRD99QPJsal77337nolVDjRwaj74osf06YOT+iT1Ufeq7ZwCWD3vKhsvbbPJwrXay12m9WlMqu0JjlgFhxM+Sn/UWuN6MFacmi3w2UjHtIh2WsIPsuct+gEHD9T66M9t304pI3Mh2eejAeLxQoa1CXZ0/Hgz0/GddnMdJjy+k/w6ZK+MHlJy1DJA8yZ2B3zUjvTSYNeYvxqdFMajA1L0cUlpKen3ZYHsP/LDedKTpUSr1EBQGKcEeyVPMbMBp8H8P5SyHP8ggvqGpX+OASA0ynnAJc9p9cfOdWl6N9FFetDTsXe7i/xsdNTKggAR9vdFUBjBFo/QPuHKi6Hn/FChRYtlihVeIihuqLQ6hY9+0WOU3I7cb/lhsUalJ2mwzGHqsLD7ygJtqAS1GASvD0gCQZb7TdlVTXjNxyNhJcfU6F5kTcQJJGhh1l9tp3HbBCGQEc5QHrG5/fu79GiF0VFdZqRnZ2TYLHUQc+eqX127NjXR04Aa+H69StAbcHBIaHV1VVrcUylAeNPZPmU6O7x4cyBLzVceAmCwgvlNcj5Y8wHqQOWT1+7P0I5dKI5FAd4gGNl12ofjoiLM4uibHXdKGJSfhOur1pjBI2uxXBUl5dWEw/qmYkU2BuakMUvvOiAdUtzmPV+cdlZ6DNmHzw/JhGyepkZnd0mgd3gRrCYwNXs4v35H+kXDm/MSofpyy+w9qNf5cgnZb87wnSYzgJoQ9DuEFi7ury8/MKkSZO+vJNDKBTuhVCvB6iwQwx5gAqHL+739wR69H5RYUoOgHT1FouubB/LwWYtn7+NTvFUyw/L8eht/IrKSR4DgAs9WHseAOdIQSm4Xe5WOwj07LD7HtlnD6TYVq2BF41mAXXtH1j3KZtYUNBknIbQqDXySKNSlLeAkkVvotdhEmyphwa7FdQ6HkxmAwQHB8HAEUOGzZ08y5cEV61/bp5n5rbxO8+aoHuEEcZmq1FMyjcQ3qNg5fnrAgF2nrVB6c0bUI18sO5Z8O4C4fXc2rXr56JyQ1hYOHz//c6r/foN6EYNx44dvpqZmdWtutoCRPPkk4/ZEDCL/ZZqYHSQ+rneSVGpj/dN6E9yqzcmwg9VshVuMCZBCI5hdV7/l77/6erRwstV992sc35OX4T4eYBtZZeuPBkR2zVao8eMXzF+pPwkb/IAGp18WGtvbrLWlF2/RDwIgKf9PQCFMXp9KPJyTH/SkqMhJ8MIWalGtqZqNa6OR8LE3MPoAj0I/Q7qi++ZloT5gJUZIm+fFAbiLZOZl1/d3NyM2T0cucOD2Fq31wN0wUSVcoAuBhYhtvIAiOrRM9bsefGVV476DIbNRtsFPbC9hOzgnZ4Ekx5LdOgREdoWAO5q2dq7W58L0LNDeaO1rOwAjiE7OG9S9oF6gIihg7Ktn/+lQDk5lYXyzKTsS4g04+BB2dIXfylAgR7wttHnBIMGDfIdwLUkwQLcrKuDBpcVdEYthHqCMYTxgBGTYAQJQ+OePXvYlxpi8cEZ53nV2nd+iIOzlWaYOVgP4SYdbP/HVhj529FgsTrh/T0O+P58MxRXXAei9x+fIoPDr702b/OqVe//18SJo9fcvFnZnJ9/YCHL9rE+OjrGvGnT1lkLFszcjPSHfXq7eRSoJmwZN3FQ5jiDTmu02dyoGDrW55jfjmE0x/5eIjsrmwvu6xHRP6NrcMbqvx1tErdM2A+bfNuPlS67bc2ZAwdC0gYOzTAEhZrUejMMnSnzBkXJ25G2hjrrz0cOnREctjWYb1RSFEEAodDHu4tjMISB9wxj2vhoUJwxaLUqpvyypwBGVxfA770ezQlhheTg7ZPnVUjHKe+R+dUofMoCK+5UAanzjNGj+RLyAPh8pdKhxP2eVp5Ao1IdaTpzZm07+ntXFyLqVPm1U+22WTBKjpQkNkadTtdqzPZmJQLZMmmT+qF5YPl4/ZzQKXnZDZ+sL/DU/PyucOBtluxiW6Tw2fo5uufzsl3rfG2b4K9P+/rbu3cvZGRkqG95GOZ3EowAUZ0+fbplnPlvbwwfzkGR58G19U2hsOsMgoUlsPfDm2/dxHBIgCa0F9WN9SBcPjiD6P3fUCefZu08fPggN2hQdhkK/TO0ei/QgaUgsJ0PvrS05D1ss6H1O4Aeyv9U2wiO+niNijP676VTCIy5k6Tc8/4n1xqeYzyMVzFeUjnbYs63mZ7lCrfX/DE2tXdyp6S0iJBOKQZZ8S32qsvnaisunipy2y6+47FuyGcf9AUF1SQnJ7f6IjJr7IWFK/6nI4m35O6O+rMHKyoq2vC3d1Gfy9a35efu9mtQVKbQrg8/PPva3r1L7AUFYMjOlp02Kb7ySwrXffDgJRjyrCF5BX4KcJfvNyofQgXuagQFPwJ/5iMgpd1zm1o41fCDJ4/GT3kxn/38aIjtOxEqTmySCtZtV6IrMhc6bBuptG3Gtq20rYzjrlfeTwIOio2NfbqwsHB16xCoDuodjcDrODkEUs4ChvYd8jIKbqMSxnmNQDCW5M556xeAKXIUdqz1WyQXWGu23Vift0r5fqrRf+048u3ovOjgmk5ylWQyHePpe5Qw7TyCwPsRGyXPtJ9cg/wC8mbxfSaO5GKyBgKvcXlUOgcWO6i0zsyiVcxTnE5acD9ILh0nOvVUQHJrPZUn90uFm7ZjHyfbjIFP6Ax89oPAxabhSJRPZNyV4Kk4B1LBQZBKq7xjUD4vCLqDDzMFbxh8N/y/BgC6mRITx/BGY6db0WHIU2UtLqYgvuRXBsCtmmM7+miVJo/vtiG/XlEanQIiwa9Aq+0vef+P5d0e2tqS309ZcazBYBiMdZEUjypz4uTmVtt5HuWLzGoEyR7F8wZ+jhavKJL/STe9t9Y/7AkAAK9YY+NtKALNnba9bMgvIS8pYFdFEX/Rd7hYyrAP992O4f/yc+j/FWAA9yiH4jk/04cAAAAASUVORK5CYII=") left top no-repeat;
					display: inline-block;
					margin-top: 4px;
					vertical-align: top;
					height: 16px;
					width: 16px;
				}

				#i-debug-panel span.time
				{
					background-position: -96px 0;
				}

				#i-debug-panel span.mem
				{
					background-position: -32px 0;
				}

				#i-debug-panel span.db
				{
					background-position: -80px 0;
				}

				#i-debug-panel span.vars
				{
					background-position: -112px 0;
				}

				#i-debug-panel span.files
				{
					background-position: -16px 0;
				}

				#i-debug-panel span.engine
				{
					background-position: -48px 0;
				}

				#i-debug-panel span.ini-values
				{
					background-position: -145px 0;
				}

				#i-debug-panel span.show-profile,
				#i-debug-panel span.hide-profile
				{
					border: none;
				}

				#i-debug-panel span.show-profile
				{
					background-position: -176px 0;
					padding: 2px 4px;
					margin-left: 9px;
				}

				#i-debug-panel span.hide-profile
				{
					background-position: -160px 0;
				}

				#i-debug-panel .panel
				{
					padding: 30px 5px 10px 5px;
				}

				#i-debug-panel .panel li
				{
					font-size: 11px;
				}

				#i-debug-panel h3
				{
					color: #CCC;
					font-size: 12px;
					padding: 5px 0 0 0;
					border-bottom: 1px solid #444;
					text-align: center;
				}

				#i-debug-panel ul.exts
				{
					max-width: 700px;
				}

				#i-debug-panel ul.exts li
				{
					width: 100px;
					float: left;
				}

				#i-ext-show
				{
					margin: 15px auto;
				}
			</style>

			<script type="text/javascript">
				function DebugPanel_Toggle(id)
				{
					var e = document.getElementById(id);
					e.style.display = (e.style.display == 'none' ? 'block' : 'none');
				}

				function DebugPanel_ShowHidePanel()
				{
					DebugPanel_Toggle('i-debug-panel-all-panels');
					DebugPanel_Toggle('i-debug-panel-list');
					DebugPanel_Toggle('i-show-profile');
				}

				function DebugPanel_ShowExtensionFuncs(id)
				{
					var e = document.getElementById(id);
					document.getElementById('i-ext-show').innerHTML = e.innerHTML;
				}
			</script>

			<div style="clear: both"></div>
			<div id="i-debug-panel">
				<span class="icon show-profile" id="i-show-profile" onclick="DebugPanel_ShowHidePanel()" style="display:none;"></span>

				<ul id="i-debug-panel-list" style="display: block;">
					<li class="show-hide-elem">
						<span class="icon hide-profile" onclick="DebugPanel_ShowHidePanel()"></span>
					</li>
					<li>
						<span class="icon time"></span>
						<?php echo number_format($this->getDiff($this->start_time), 6); ?> s
					</li>
					<li>
						<span class="icon mem"></span>
					</li>
					<?php if ( class_exists( 'Debug_Database_Logger' )): ?>
						<li>
							<span class="icon db"></span>
							<a href="javascript:DebugPanel_Toggle('i-database-log')">sql <span class="small">(<?php echo count( Debug_Database_Logger::$log ); ?>)</span></a>
						</li>
					<?php endif ?>
					<li>
						<span class="icon vars"></span>
						<a href="javascript:DebugPanel_Toggle('i-vars-log')">vars <span class="small">(G: <?php echo count($_GET)?> / P: <?php echo count($_POST)?> / C: <?php echo count($_COOKIE)?> / F: <?php echo count($_FILES)?>)</span></a>
					</li>
					<li>
						<span class="icon files"></span>
						<a href="javascript:DebugPanel_Toggle('i-files')">files <span class="small">(<?php echo 1 + count($debug->Files())?>)</span></a>
					</li>
					<li>
						<span class="icon engine"></span>
						<a href="javascript:DebugPanel_Toggle('i-engine')">engine</a>
					</li>
					<li>
						<span class="icon ini-values"></span>
						<a href="javascript:DebugPanel_Toggle('i-ini-values')">ini + exts</a>
					</li>
				</ul>

				<div id="i-debug-panel-all-panels" style="display: block;">
					<?php if ( class_exists( 'Debug_Database_Logger' )): ?>
						<div id="i-database-log" class="panel" style="display: none">
							<table cellpadding="0" cellspacing="0">
								<tr>
									<th>№</th>
									<th>query &amp; file &amp; call line</th>
								</tr>
								<?php foreach (Debug_Database_Logger::$log as $k => $v):?>
									<tr>
										<td rowspan="2"><?php echo $k?></td>
										<td>QUERY: <strong><?php echo $v['sql']; ?></strong></td>
									</tr>
									<tr>
										<td>FILE: <strong><?php echo $v['file']; ?></strong> line <strong><?php echo $v['line']; ?></strong></td>
									</tr>
								<?php endforeach?>
								<tr class="total">
									<td colspan="2" class="center"><span>Total</span> <?php echo count(Debug_Database_Logger::$log); ?> <span>queries</span></td>
								</tr>
							</table>
						</div>
					<?php endif ?>

					<div id="i-vars-log" class="panel" style="display: none">
						<ul>
							<li>
								<?php $this->showVarPanel( '$_GET', 'i-get-log', $_GET)?>
							</li>
							<li>
								<?php $this->showVarPanel( '$_POST', 'i-post-log', $_POST)?>
							</li>
							<li>
								<?php $this->showVarPanel( '$_COOKIE', 'i-cookie-log', $_COOKIE)?>
							</li>
							<?php if (session_id()):?>
								<li>
									<?php $this->showVarPanel( '$_SESSION', 'i-session-log', $_SESSION)?>
								</li>
							<?php endif?>
							<li>
								<?php $this->showVarPanel( '$_SERVER', 'i-server-log', $_SERVER)?>
							</li>
							<li>
								<a href="javascript:DebugPanel_Toggle('i-files-log')">$_FILES <span>(<?php echo count($_FILES)?>)</span></a>
								<div id="i-files-log" style="display: none">
									<table cellpadding="0" cellspacing="0">
										<tr>
											<th>№</th>
											<th>field name</th>
											<th>name</th>
											<th>type</th>
											<th>tmp_name</th>
											<th>error</th>
											<th>size</th>
										</tr>
										<?php if (count($_FILES)):?>
											<?php $i = 0; foreach ($_FILES as $k => $v):?>
												<tr>
													<td class="num"><?php echo ++$i?></td>
													<td><?php echo $k?></td>
													<td><?php echo $v['name']?></td>
													<td><?php echo $v['type']?></td>
													<td><?php echo $v['tmp_name']?></td>
													<td><?php echo $v['error']?></td>
													<td><?php echo $v['size']?></td>
												</tr>
											<?php endforeach?>
										<?php else:?>
											<tr>
												<td colspan="7" class="center">Empty</td>
											</tr>
										<?php endif?>
									</table>
								</div>
							</li>
						</ul>
						<?php
						if (function_exists('xdebug_dump_superglobals'))
							xdebug_dump_superglobals();
						?>
					</div>

					<div id="i-files" class="panel" style="display: none">
						<table cellpadding="0" cellspacing="0">
							<tr>
								<th>№</th>
								<th>file</th>
								<th>size</th>
								<th>lines</th>
							</tr>
							<?php $i = 0; foreach ($debug->Files() as $f):?>
								<tr>
									<td class="num"><?php echo ++$i?></td>
									<td><?php echo str_replace(BASEPATH, '<span>' . BASEPATH . '</span>', $f['file'])?></td>
									<td><?php echo $f['size']?></td>
									<td><?php echo $f['lines']?></td>
								</tr>
							<?php endforeach?>
							<tr class="total">
								<td></td>
								<td class="center"><span>Total</span> <?php echo count($debug->Files())?> <span>files</span></td>
								<td><?php echo $debug->TotalFileSize()?></td>
								<td><?php echo $debug->TotalFileLines()?></td>
							</tr>
						</table>
					</div>

					<div id="i-engine" class="panel" style="display: none">
						<table cellpadding="0" cellspacing="0">
							<?php /*<tr>
								<td>language</td>
								<td><?php echo $g_arrLangs[LANG]['name']?> <strong>(<?php echo LANG?>)</strong></td>
							</tr> */ ?>
							<tr>
								<td>current url</td>
								<td><?php lnk('.'); ?></td>
							</tr>
							<tr>
								<td>request</td>
								<td><?php echo $_query;?></td>
							</tr>
							<tr>
								<td>db connection</td>
								<td><?php echo is_null($_db) ? 'Maybe' : 'Yes';?></td>
							</tr>
						</table>
					</div>

					<div id="i-ini-values" class="panel" style="display: none">
						<h3>Load extensions</h3>
						<ul class="exts">
							<?php foreach (get_loaded_extensions() as $v):?>
								<li>
									<a href="javascript:DebugPanel_ShowExtensionFuncs('i-ext-<?php echo md5($v)?>')">
										<?php echo $v?>
									</a>
									<div style="display:none" id="i-ext-<?php echo md5($v)?>">
										<h3>Function in extension: <?php echo $v?></h3>
										<?php
											$funcs = get_extension_funcs($v);
											$funcs = empty($funcs) ? array() : $funcs;

											if ($funcs):
										?>
											<table cellpadding="0" cellspacing="0">
												<?php foreach (get_extension_funcs($v) as $k => $func):?>
													<tr>
														<td><?php echo $k++?></td>
														<td><?php echo $func?></td>
													</tr>
												<?php endforeach?>
											</table>
										<?php
											endif;
										?>
									</div>
								</li>
							<?php endforeach?>
						</ul>
						<div style="clear: both"></div>
						<div id="i-ext-show"></div>

						<h3>Php.ini</h3>
						<table cellpadding='0' cellspacing='0'>
							<tr>
								<th>name </th>
								<th>global val</th>
								<th>local val</th>
								<th>access</th>
							</tr>
							<?php
								foreach (ini_get_all() as $k => $v)
								{
									?>
										<tr>
											<td><?php echo $k?></td>
											<td><?php echo $this->VarDump($v['global_value'])?></td>
											<td><?php echo $this->VarDump($v['local_value'])?></td>
											<td><?php echo Debug_DebugPanel::ShowPhpIniAccess($v['access'])?></td>
										</tr>
									<?php
								}
							?>
						</table>
					</div>
				</div>

				<div style="clear: both"></div>
			</div>
		<?php
		$debugger = ob_get_contents();
		ob_end_clean();
		$debugger = str_replace('<span class="icon mem"></span>', '<span class="icon mem"></span>' . $debug->MemoryUsage(memory_get_usage()), $debugger);
		if (strpos($content, '</body>'))
			$content = str_replace('</body>', $debugger . '</body>', $content);
		else
			$content .= $debugger;

		echo $content;
	}
}

$_d = new Debug_Debugger;