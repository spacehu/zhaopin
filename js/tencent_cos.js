/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var cos = new COS({
    getAuthorization: function (options, callback) {
        // 异步获取临时密钥
        $.get('../lib/cos-js-sdk-v5-master/server/sts.php', {
            bucket: options.Bucket,
            region: options.Region
        }, function (data) {
            callback({
                TmpSecretId: data.credentials.tmpSecretId,
                TmpSecretKey: data.credentials.tmpSecretKey,
                XCosSecurityToken: data.credentials.sessionToken,
                ExpiredTime: data.expiredTime
            });
        });
    }
});